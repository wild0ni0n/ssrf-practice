from http.server import HTTPServer, BaseHTTPRequestHandler
import logging

PORT = 8888

class MyHTTPRequestHandler(BaseHTTPRequestHandler):
    def _response(self):
        self.send_response(200)
        self.send_header('Content-Type', 'text/plain; charset=utf-8')
        self.end_headers()

    def viewlog(self):
        self._response()
        logdata = ""
        with open('/var/log/server.log', mode='r', encoding='utf-8') as f:
            logdata = f.read()
        self.wfile.write(bytes(logdata, "utf-8"))

    def do_GET(self):
        if self.path == '/view-log':
            return self.viewlog()
        logging.info("GET request,\nPath: %s\nHeaders:\n%s\n", str(self.path), str(self.headers))
        self._response()
        self.wfile.write(b'hi.')
        
    def do_POST(self):
        content_length = int(self.headers['Content-Length'])
        post_data = self.rfile.read(content_length)
        logging.info("POST request,\nPath: %s\nHeaders:\n%s\n\nBody:\n%s\n",
                str(self.path), str(self.headers), post_data.decode('utf-8'))
        self._response()
        self.wfile.write(b'hi.')

def run(server_class=HTTPServer, handler_class=MyHTTPRequestHandler):
    logging.basicConfig(filename='/var/log/server.log', encoding='utf-8', level=logging.INFO)
    server_address = ('', PORT)
    with server_class(server_address, handler_class) as httpd:
        httpd.serve_forever()

if __name__ == "__main__":
    run()