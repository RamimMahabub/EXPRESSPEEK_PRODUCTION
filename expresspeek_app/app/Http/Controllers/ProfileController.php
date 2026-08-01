<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (! empty($validated['phone'])) {
            $countryCode = $validated['phone_country_code'] ?: ($user->phone_country_code ?: '+880');
            $phone = User::normalizePhone($countryCode, $validated['phone']);

            if (! preg_match('/^\+[1-9]\d{7,14}$/', $phone)) {
                throw ValidationException::withMessages([
                    'phone' => 'Enter a valid mobile number including the correct country code.',
                ]);
            }

            if (User::query()->where('phone', $phone)->whereKeyNot($user->getKey())->exists()) {
                throw ValidationException::withMessages([
                    'phone' => 'This phone number is already connected to another account.',
                ]);
            }

            $user->phone_country_code = $countryCode;
            $user->phone = $phone;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $newAvatarPath = null;
        $oldAvatarPath = $user->avatar_path;

        try {
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $directory = public_path('uploads/avatars');
                File::ensureDirectoryExists($directory, 0755, true);

                $filename = $user->getKey().'-'.Str::uuid().'.webp';
                $newAvatarPath = 'uploads/avatars/'.$filename;
                $this->optimizeAvatar(
                    $request->file('avatar')->getPathname(),
                    public_path($newAvatarPath),
                    $directory
                );
                $user->avatar_path = $newAvatarPath;
            }

            $user->save();
        } catch (Throwable $exception) {
            if ($newAvatarPath) {
                File::delete(public_path($newAvatarPath));
            }

            report($exception);

            $message = match (true) {
                str_contains($exception->getMessage(), 'avatar_path') => 'Profile photo database support is not installed yet. Ask an administrator to run the secure system update.',
                str_starts_with($exception->getMessage(), 'Profile photo') => $exception->getMessage(),
                default => 'We could not process that photo. Please try a different JPG, PNG, or WebP image.',
            };

            return back()->withInput()->with('error', $message);
        }

        if ($newAvatarPath && $oldAvatarPath && str_starts_with($oldAvatarPath, 'uploads/avatars/')) {
            File::delete(public_path($oldAvatarPath));
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->avatar_path && str_starts_with($user->avatar_path, 'uploads/avatars/')) {
            File::delete(public_path($user->avatar_path));
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Use the best image driver available on the current hosting account.
     */
    private function optimizeAvatar(string $sourcePath, string $targetPath, string $directory): void
    {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            throw new RuntimeException('Profile photo optimization is unavailable. Ask your hosting provider to enable PHP GD with WebP support.');
        }

        if (! is_writable($directory)) {
            throw new RuntimeException('Profile photo storage is not writable. In cPanel File Manager, set public/uploads and public/uploads/avatars permissions to 755, then try again.');
        }

        $imageInfo = @getimagesize($sourcePath);
        if (! $imageInfo || empty($imageInfo[0]) || empty($imageInfo[1])) {
            throw new RuntimeException('Profile photo could not be decoded. Please upload a standard JPG, PNG, or WebP image.');
        }

        if (($imageInfo[0] * $imageInfo[1]) > 20_000_000) {
            throw new RuntimeException('Profile photo dimensions are too large for this server. Please resize it below 20 megapixels and try again.');
        }

        $contents = @file_get_contents($sourcePath);
        $source = $contents !== false ? @imagecreatefromstring($contents) : false;
        if ($source === false) {
            throw new RuntimeException('Profile photo could not be decoded. Please export it as a standard JPG or PNG and try again.');
        }

        try {
            $source = $this->orientGdImage($source, $sourcePath, $imageInfo['mime'] ?? '');
            $sourceWidth = imagesx($source);
            $sourceHeight = imagesy($source);
            $cropSize = min($sourceWidth, $sourceHeight);
            $sourceX = (int) floor(($sourceWidth - $cropSize) / 2);
            $sourceY = (int) floor(($sourceHeight - $cropSize) / 2);

            $avatar = imagecreatetruecolor(512, 512);
            if ($avatar === false) {
                throw new RuntimeException('Profile photo could not be prepared because the server has insufficient image memory. Please use a smaller photo.');
            }

            imagealphablending($avatar, false);
            imagesavealpha($avatar, true);
            $transparent = imagecolorallocatealpha($avatar, 0, 0, 0, 127);
            imagefill($avatar, 0, 0, $transparent);

            if (! imagecopyresampled($avatar, $source, 0, 0, $sourceX, $sourceY, 512, 512, $cropSize, $cropSize)) {
                throw new RuntimeException('Profile photo could not be resized. Please try a different image.');
            }

            if (! @imagewebp($avatar, $targetPath, 82)) {
                throw new RuntimeException('Profile photo could not be saved. Check that public/uploads/avatars is writable in cPanel File Manager.');
            }
        } finally {
            if (isset($avatar) && $avatar !== false) {
                imagedestroy($avatar);
            }

            imagedestroy($source);
        }
    }

    private function orientGdImage($image, string $sourcePath, string $mime)
    {
        if ($mime !== 'image/jpeg' || ! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($sourcePath);
        $orientation = is_array($exif) ? ($exif['Orientation'] ?? 1) : 1;
        $angle = match ((int) $orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);
        if ($rotated === false) {
            return $image;
        }

        imagedestroy($image);

        return $rotated;
    }
}
