from __future__ import with_statement
import Webwidgets, contextlib, math
from Cursor import cursor

class RowsComposite(Webwidgets.RowsComposite):
    """This is a version of L{RowsComposite} that fetches the rows
    from an SQL-database."""
    
    debug_queries = False
    debug_expand_info = False
    debug_rows = False

    class WwModel(Webwidgets.RowsComposite.WwModel):
        table = ""
        db_where = ""
    
    class SourceFilters(Webwidgets.RowsComposite.SourceFilters):
        WwFilters = Webwidgets.RowsComposite.SourceFilters.WwFilters + ['DB2Filter']

        class DB2Filter(Webwidgets.Filter):
            """This filter provides rows from a DB2 back-end."""

            non_memory_storage = True

            def get_num_rows(self, output_options = {}, **kw):
                with cursor(self.session.program.conn) as cur:
                    cur.execute("select count(*) from %s" % (self.table,))
                    return cur.fetchone()[0]

            def get_rows(self, all = False, output_options = {}, **kw):
                with cursor(self.session.program.conn) as cur:
                    limit = ""
                    if not all and self.rows_per_page:
                        self.pages = self.get_pages()
                        limit = "limit %(limit)s offset %(offset)s" % {
                            'offset': (self.page - 1) * self.rows_per_page,
                            'limit': self.rows_per_page
                            }

                    cur.execute("select * from %(table)s %(sort)s %(limit)s" % {
                        'table': self.table,
                        'sort': Webwidgets.sort_to_order_by(self.sort, quote='"') or '',
                        'limit': limit
                        })
                    columns = [dsc[0] for dsc in cur.description]
                    return [dict(zip(columns, row)) for row in cur]

            def get_row_by_id(self, row_id, **kwargs):
                with cursor(self.session.program.conn) as cur:
                    cur.execute("select * from %(table)s where id = %(id)s" % {
                        'table': self.table,
                        'id': row_id})
                    return dict(zip([dsc[0] for dsc in cur.description],
                                    cur.fetchone()))

            def get_row_id(self, row):
                return str(row['id'])

            def get_pages(self):
                if not self.rows_per_page:
                    return 1
                return int(math.ceil(float(self.get_num_rows()) / self.rows_per_page))

            def column_is_sortable(self, column):
                return True
