#! /bin/env python
# -*- coding: UTF-8 -*-
# vim: set fileencoding=UTF-8 :

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

class RowsListInput(Webwidgets.RowsListInput, RowsMod.RowsComposite):
    debug_queries = False
    debug_expand_info = False

    class WwModel(RowsMod.RowsComposite.WwModel, Webwidgets.RowsListInput.WwModel):
        pass
    
    class SourceFilters(RowsMod.RowsComposite.SourceFilters, Webwidgets.RowsListInput.SourceFilters):
        WwFilters = Webwidgets.RowsListInput.SourceFilters.WwFilters + ['DB2Filter']

class RowsSingleValueListInput(RowsListInput, Webwidgets.RowsSingleValueListInput):
    class WwModel(Webwidgets.RowsSingleValueListInput.WwModel, RowsListInput.WwModel):
        pass
