def application(environ, start_response):
    body = b'ExpressPeek Python runtime is ready.'
    start_response('200 OK', [
        ('Content-Type', 'text/plain; charset=utf-8'),
        ('Content-Length', str(len(body))),
    ])
    return [body]
