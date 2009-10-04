#! /bin/env python
# -*- coding: utf-8 -*-
# vim: set fileencoding=utf-8 :

# From Worm
# Copyright (C) 2008 FreeCode AS, Egil Moeller <egil.moeller@freecode.no>

# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.

# This program is distributed in the hope that it will be useful, but
# WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
# General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
# USA

import Webwidgets, RowsMod

class ReadonlyTable(Webwidgets.Table, RowsMod.RowsComposite):
    debug_queries = False
    debug_expand_info = False

    class WwModel(RowsMod.RowsComposite.WwModel, Webwidgets.Table.WwModel):
        pass
    
    class SourceFilters(RowsMod.RowsComposite.SourceFilters, Webwidgets.Table.SourceFilters):
        WwFilters = Webwidgets.Table.SourceFilters.WwFilters + ['DB2Filter']

class ExpandableReadonlyTable(ReadonlyTable, Webwidgets.ExpandableTable):
    """This widget allows rows to contain a "subtree row" in
    L{ww_expansion} that is inserted below the row if
    L{ww_is_expanded} is set on the row. It also adds an expand button
    that allows the user to set/reset L{ww_is_expanded}.
    """
class ExpansionReadonlyTable(ExpandableReadonlyTable, Webwidgets.ExpansionTable):
    """This widget allows any row to be "expanded" by inserting an
    extra row containing an instance of the L{ExpansionViewer} widget
    after the row if L{ww_is_expanded} is set on the row. It also adds
    an expand button that allows the user to set/reset
    L{ww_is_expanded}."""

    class RowsRowModelWrapper(ExpandableReadonlyTable.RowsRowModelWrapper):
        WwFilters = ["ExpansionFilter"] + ExpandableReadonlyTable.RowsRowModelWrapper.WwFilters

        class ExpansionFilter(Webwidgets.Filter):
            def __init__(self, *arg, **kw):
                Webwidgets.Filter.__init__(self, *arg, **kw)
                if hasattr(self, 'is_expansion'): return
                self.ww_expansion = {
                    'is_expansion': True,
                    'ww_functions': [],
                    'ww_expanded': self.table.ExpansionViewer(
                    self.table.session, self.table.win_id,
                    parent_row = self.object)}

