#! /usr/bin/python

import Config, polib, psycopg2, sys, os.path, re

conn = psycopg2.connect(Config.dsn)
cur = conn.cursor()

def insert_anything(table, **args):
    options = {"table": table,
               "where_args": " and ".join("%s = %%(%s)s" % (key, key) for key in args.iterkeys()),
               "columns": ", ".join(args.iterkeys()),
               "column_values": ", ".join("%%(%s)s" % (arg,) for arg in args.iterkeys())}
    cur.execute("select id from %(table)s where %(where_args)s" % options,
		args)
    if cur.rowcount:
        return cur.fetchone()[0]
    cur.execute("insert into %(table)s (%(columns)s) values (%(column_values)s)" % options,
                args)
    cur.execute("select id from %(table)s where %(where_args)s" % options,
		args)
    return cur.fetchone()[0]


def insert_language(symbol, name = ""):
    language_id = insert_anything("languages", symbol=symbol)
    if name:
        cur.execute("update languages set name = %(name)s where id = %(id)s",
                    {"id":language_id})
    return language_id

def insert_metadata(name):
    return insert_anything("metadatas", name=name)

def insert_package(name, version):
    return insert_anything("packages", name=name, version=version)

def insert_msgid(string):
    return insert_anything("msgids", string=string)

def insert_msgstr(string):
    return insert_anything("msgstrs", string=string)

def insert_word(string):
    return insert_anything("words", string=string)

def insert_msgstr_word(msgstr, word):
    return insert_anything("msgstr_words", msgstr=msgstr, word=word)

def insert_translation(mofile, language, msgid, msgstr):
    return insert_anything("translations", mofile=mofile, language=language, msgid=msgid, msgstr=msgstr)

if sys.argv[1] == "languages":
    file = open(sys.argv[2])
    for line in file:
	line = line.strip()
	if line.startswith("#") or not line: continue
	name, symbol = line.split()
	symbol = symbol.split(".")[0]
        print "INSERT", symbol, name
        insert_language(symbol.decode("latin1"), name.decode("latin1"))
    file.close()

if sys.argv[1] == "mofile":
    package_name = sys.argv[2]
    package_version = sys.argv[3]
    mofile_path = sys.argv[4]

    word_re = re.compile(r"\W+")

    package_id = insert_package(package_name, package_version)
    args = {"package": package_id, "path": mofile_path}
    cur.execute("select id from mofiles where package = %(package)s and path = %(path)s",
                args)
    if not cur.rowcount:
        cur.execute("insert into mofiles (package, path) values (%(package)s, %(path)s)",
                    args)
        cur.execute("select id from mofiles where package = %(package)s and path = %(path)s",
                    args)
        mofile_id = cur.fetchone()[0]

        path, mofile = os.path.split(mofile_path)
        path, lc_part = os.path.split(path)
        path, language = os.path.split(path)

        assert lc_part.startswith("LC_")
        language_id = insert_language(language)
        
        mofile = polib.mofile(mofile_path)
        charset = mofile.charset()

        for key, value in mofile.metadata.iteritems():
            cur.execute("insert into mofile_metadatas (mofile, metadata, value) values (%(mofile)s, %(metadata)s, %(value)s)",
                        {"mofile": mofile_id,
                         "metadata": insert_metadata(key.decode(charset)),
                         "value": value.decode(charset)})

        for item in mofile:
            msgid = item.msgid.decode(charset)
            msgstr = item.msgstr.decode(charset)

            msgid_id = insert_msgid(msgid)
            msgstr_id = insert_msgstr(msgstr)

            for word in word_re.split(msgstr):
                if word:
                    word_id = insert_word(word)
                    insert_msgstr_word(msgstr_id, word_id)
                    
            insert_translation(mofile_id, language_id, msgid_id, msgstr_id)

conn.commit()
