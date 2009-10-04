#! /bin/env python
# -*- coding: UTF-8 -*-
# vim: set fileencoding=UTF-8 :

# Webwidgets web developement framework
# Copyright (C) 2007 FreeCode AS, Egil Moeller <egil.moeller@freecode.no>

# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

import Webwidgets, psycopg2
import FOSSTrans.Config, FOSSTrans.UI

Webwidgets.Program.Session.debug_fields = False
Webwidgets.Program.Session.debug_field_input = False
Webwidgets.Program.Session.debug_field_registrations = False
Webwidgets.Program.Session.debug_receive_notification = False
Webwidgets.Program.Session.debug_arguments = False
Webwidgets.Program.profile = False
Webwidgets.Wwml.debug_import = True
Webwidgets.Widgets.Base.debug_exceptions = True
Webwidgets.Widgets.Base.log_exceptions = True
Webwidgets.LogIn.debug_errors = True

class index(Webwidgets.Program):
    def __init__(self, *arg, **kw):
        Webwidgets.Program.__init__(self, *arg, **kw)
        self.conn = psycopg2.connect(FOSSTrans.Config.dsn)
    
    class Session(Webwidgets.Program.Session):
        def new_window(self, win_id):
            return FOSSTrans.UI.MainWindow(self, win_id)
