antlr4 = java -Xmx500M -cp "/usr/local/lib/antlr-4.7.1-complete.jar:$CLASSPATH" org.antlr.v4.Tool
antlr = java -Xmx500M -cp "/usr/local/lib/antlr-4.9.2-complete.jar:$CLASSPATH" org.antlr.v4.Tool
grun = java org.antlr.v4.gui.TestRig

start:
	$(antlr4) search.g4

visitor:
	$(antlr4) -no-listener -visitor search.g4

build:
	javac search*.java

show:
	$(grun) search start -gui

gen:
    $(antlr) -Dlanguage=PHP -no-listener -visitor search.g4

clean:
	rm -rf *.class search*.java search*.php *.interp *.tokens
