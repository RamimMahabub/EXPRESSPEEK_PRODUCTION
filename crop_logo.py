from PIL import Image, ImageChops

def trim(im):
    bg = Image.new(im.mode, im.size, im.getpixel((0,0)))
    diff = ImageChops.difference(im, bg)
    diff = ImageChops.add(diff, diff, 2.0, -100)
    bbox = diff.getbbox()
    if bbox:
        return im.crop(bbox)
    return im

img_path = r"c:\Users\Admin\Downloads\ChatGPT Image Jul 2, 2026, 01_14_53 AM.png"
im = Image.open(img_path)
trimmed_im = trim(im)
# Add a small padding of 10 pixels around the cropped logo
padded = Image.new(trimmed_im.mode, (trimmed_im.width + 20, trimmed_im.height + 20), (255,255,255))
padded.paste(trimmed_im, (10, 10))
# Save as WebP
padded.save("d:/projects/expresspeek_app/expresspeek_app/public/images/express-peek-logo.webp", "WEBP", quality=90)
print("Cropped successfully!")
