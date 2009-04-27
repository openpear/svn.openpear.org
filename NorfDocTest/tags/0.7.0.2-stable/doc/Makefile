INPUT_CHARCODE  = utf-8
OUTPUT_CHARCODE = utf-8
RST             = rst2html.py
RST_FLAGS       = -i $(INPUT_CHARCODE) -o $(OUTPUT_CHARCODE) \
	--stylesheet-path=$(CSS) --link-stylesheet
CSS             = doctest.css

TEXTS   = doctest_ja.html doctest_en.html

.SUFFIXES: .txt .html

.txt.html:
	$(RST) $(RST_FLAGS) $< $@

all: $(TEXTS)
