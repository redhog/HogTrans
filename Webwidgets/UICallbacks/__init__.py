from __future__ import with_statement

import Webwidgets, math, FOSSTrans.Utils.ListMod, FOSSTrans.Utils.Table
from FOSSTrans.Utils.Cursor import cursor

class NotEmpty(object):
    invalid_not_empty = "Required field"

    def validate_not_empty(self):
        return not not self.ww_filter.value

class LanguageInput(FOSSTrans.Utils.ListMod.RowsSingleValueListInput, NotEmpty):
    columns = Webwidgets.Utils.OrderedDict([('symbol', 'Sym'), ('name', 'Name')])
    column_separator = ' - '
    table = "languages"
    sort = [('symbol', 'asc')]

class MainWindow(object):
    class Body(Webwidgets.ApplicationWindow.Body, Webwidgets.DirectoryServer):
        class SearchModes(object):
            class SearchWords(object):
                class Search(object):
                    class SrcWord(object):
                        class Field(object):
                            invalid_found = "The specified word was not found in our database"
                            def validate_found(self):
                                with cursor(self.session.program.conn) as cur:
                                    cur.execute("select * from words where string = '%(string)s'" % {
                                        'string': self.value})
                                    return not not cur.fetchone()
                                
                    class SrcLanguage(object):
                        class Field(LanguageInput): pass
                        
                    class DstLanguage(object):
                        class Field(LanguageInput): pass

                class Actions(object):
                    class Search(object):
                        def clicked(self, path):
                            search = self + "2/Search"
                            src_word = search + "SrcWord-Field"
                            src_lang = search + "SrcLanguage-Field"
                            dst_lang = search + "DstLanguage-Field"
                            result = self + "2/Result-Field"

                            if not search.validate():
                                return

                            with cursor(self.session.program.conn) as cur:
                                cur.execute("select * from words where string = '%(string)s'" % {
                                    'string': src_word.value})
                                src_word_id = cur.fetchone()[0]

                                result.table = result.table_template % {
                                    'src_word_id': src_word_id,
                                    'src_lang': src_lang.value['id'],
                                    'dst_lang': dst_lang.value['id']
                                    }
                            result.ww_filter.reread()

                    class Lucky(object):
                        pass
                    
                class Result(object):
                    class Field(FOSSTrans.Utils.Table.ReadonlyTable):
                        table = """(select
                                     true as id,
                                     true as dst_word,
                                     true as value,
                                     true as weight,
                                     true as reverse_weight,
                                     true as count,
                                     true as reverse_count,
                                     true as total,
                                     true as reverse_total
                                    where
                                     false) as x"""

                        table_template = """(select
                                              words.id,
                                              words.string as dst_word,
                                              value,
                                              weight,
                                              reverse_weight,
                                              count,
                                              reverse_count,
                                              total,
                                              reverse_total
                                             from
                                              word_translation_value,
                                              words
                                             where
                                                  src_word = '%(src_word_id)s'
                                              and src_language = '%(src_lang)s'
                                              and dst_language = '%(dst_lang)s'
                                              and dst_word = words.id) as x"""

                        def function(self, path, function, row_id):
                            row = self.ww_filter.get_row_by_id(row_id)
                            if function == "reverse":
                                search = self + "2/Search"
                                src_word = search + "SrcWord-Field"
                                src_lang = search + "SrcLanguage-Field"
                                dst_lang = search + "DstLanguage-Field"

                                src_word.ww_filter.value = row.ww_filter.dst_word
                                src_lang.ww_filter.value, dst_lang.ww_filter.value = dst_lang.ww_filter.value, src_lang.ww_filter.value
                                
                            elif function == "examples":
                                pass

        def draw(self, output_options):
            Webwidgets.HtmlWindow.register_style_link(
                self,
                self.calculate_url(
                {'transaction': output_options['transaction'],
                 'widget_class': 'FOSSTrans.UICallbacks.MainWindow.Body',
                 'location': ['FOSSTrans.css']},
                {}))
            return Webwidgets.ApplicationWindow.Body.draw(self, output_options)
