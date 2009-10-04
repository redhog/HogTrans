import contextlib

@contextlib.contextmanager
def cursor(conn, **kw):
    cur = conn.cursor()
    try:
        yield cur
    finally:
        cur.close()
        
