import re

f = open("test_html_page.html", "r")
var = f.read()

var = var.replace("\n", "")
var = re.sub(' +',' ',var)
print(var) 

