import sys
import re
import xml.etree.ElementTree as ET
import string


valid_instruction = [ "move", "int2char", "strlen", "type", "not",
 "defvar", "pops" , "call", "jump", "label", "pushs", "write", 
 "exit", "dprint", "add", "sub", "mul", "idiv", "lt", "gt", "eq",
 "and", "or", "stri2int", "concat", "getchar", "setchar", "jumpifeq",
 "jumpifneq", "read", "createframe", " break", "pushframe", "popframe"
 "return"]

valid_types = ["int", "bool", "string", "nil", "label", "type", "var"]


#
# Prints help message
def help():
    print("execution: ..........    python3 interpret.py [command]")
    print("execution: ..........    python3 interpret.py --help")
    print(".......................................................")
    print("     --help  ......................     Help message.")
    print("     --source=file .................    XML source.")
    print("     --input=file  .................    Inputs for interpretation")
    print(".......................................................")
    print(" If there is no --input or --source reads from STDIN.")
    return 0


def main():
    # parse arguments 
    arg = Arg_parse(True, "", "", "")
    
    ## files is object where program valid files and reads from them
    files = Files(False, (arg.get_in_file()), (arg.get_so_file()), arg.get_std_read())
    files.files_exists()

    ## parse xml using xml library
    root = files.xml_parse()
    files.xml_validation(root)
##
#  Parse arguments and store input and source file 
class Arg_parse:
    
    def __init__(self, parsing, in_f, so_f, re_f):
        if parsing == True:
            self.__input_file = ""
            self.__source_file = ""
            self.__parse_arguments(sys.argv)
            self.__read_from_stdin: bool
        else:
            self.__input_file = in_f
            self.__source_file = so_f
            self.__read_from_stdin = re_f
                    

    def get_in_file(self):
        return self.__input_file    

    def get_so_file(self):
        return self.__source_file   

    def get_std_read(self):
        return self.__read_from_stdin

    def set_in_file(self, val):
        self.__input_file = val
    
    def set_so_file(self, val):
        self.__source_file = val
    
    def set_std_file(self, val):
        self.__read_from_stdin = val
    
    ##
    # Parse arguments that are stored in args
    # If error or --help exit() program
    # return file_path, command
    def __parse_arguments(self, args):
        self.set_std_file(True)
        if len(args) > 3:
            sys.stderr.write("To many arguments.\n")
            exit(10)
        for a in args[1:]:
            if a == "--help" or a == "-h":
                if len(args) != 2:
                    sys.stderr.write("Can't combine help with other params.\n")
                    exit(10)
                help()
                exit(0)
            elif a.startswith("--source="):
                temp = a.split('=')
                self.set_so_file(self.__string_assemble(temp))
                self.set_std_file(False)
            elif a.startswith("--input="):
                temp = a.split('=')
                self.set_in_file(self.__string_assemble(temp))
                self.set_std_file(False)
            else:
                sys.stderr.write("Unknown \n")
                exit(10)


    ##
    # return string from array memebers concatenate with = symbol
    def __string_assemble(self, array):
        if len(array) == 2:
            return array[1]
        out = array[1]
        for a in array[2:]:
            out = out + "=" + a
        return out


##
# Read from input files validates them etc ...
# Files names are stored in argpase 
class Files(Arg_parse):

    def __init__(self, parsing, in_f, so_f, re_f):
        super().__init__(parsing, in_f, so_f, re_f)
        self.__last_instr_order = 0

    ## need last_instr_order to chceck if instruction orders in xml are valid 
    def set_last_instr(self, val):
        self.__last_instr_order = val
    
    def get_last_instr(self):
        return self.__last_instr_order
    
    ##
    # check if file exist
    def file_exist(self, file):
        if file == "":
            return
        try:
            f = open(file)
        except :
            sys.stderr.write("File: " + file + " not accessible\n")
            exit(10)    
        f.close()
    ##
    # Call file exist for input files 
    def files_exists(self):
        if self.get_std_read() == False:
            self.file_exist(self.get_in_file())
            self.file_exist(self.get_so_file())

    ##
    # Parse xml using xml libr
    # return xml root 
    def xml_parse(self):
        try:
            tree = ET.parse(self.get_so_file())
            return tree.getroot()
        except:
            sys.stderr.write("Bad xml format\n")
            exit(32)

    ##
    # Valid whole xml 
    def xml_validation(self, root): #<program>
        self.xml_valid_root(root)
        for child in root:          #<instruction>
            self.xml_valid_instr(child)
            arg_order = 1
            for ar in child:        #<arg>
                self.xml_valid_arg(ar, arg_order)
                arg_order += 1
    ##
    # Check if instruction is valid
    # - opcode is valid?
    # - order is ok?
    # if instruction has args chceck if arg has order and type 
    def xml_valid_instr(self, inst):
        opcode_flag = False
        order_flag = False
        for a in inst.attrib:
            ia = (inst.attrib[a]).lower()
            a = a.lower()
            if a == "opcode":
                if ia not in valid_instruction:
                    sys.stderr.write("Not valid instruction\n")
                    exit(32)
                opcode_flag = True
                continue
            elif a == "order":
                try:
                    ia = int(ia)
                except:
                    sys.stderr.write("Bad instruction order\n")
                    exit(32)
                if ia <= self.get_last_instr():
                    sys.stderr.write("Bad instruction order\n")
                    exit(32)
                self.set_last_instr(ia)
                order_flag = True
                continue            
            else:
                sys.stderr.write("Bad instruction flag\n")
                exit(32)
        if order_flag == False or opcode_flag == False:
            sys.stderr.write("Opcode or order missing\n")
            exit(32)


    ##
    # valid argument of instruction 
    def xml_valid_arg(self, arg, order):
        pat = re.compile('arg' + str(order))
        if not pat.match(arg.tag):
            sys.stderr.write("Unvalid arg \n")
            exit(32)

        type_flag = False
        for a in arg.attrib:
            ia = (arg.attrib[a]).lower()
            a = a.lower()
            if a == "type":
                if ia not in valid_types:
                    sys.stderr.write("Invalid arg type \n")
                    exit(32)
                type_flag = True
            else:
                sys.stderr.write("Invalid arg attribut \n")
                exit(32)
            print(a)
            print(ia)

        if type_flag == False:
            sys.stderr.write("Missing arg type flag \n")
            exit(32)

        return


    ##
    # valid root attributes which is <program>
    def xml_valid_root(self, root):
        if root.tag != "program":
            sys.stderr.write("Invalid XML root\n")
            exit(31)

        language_flag = False
        for a in root.attrib:
            ra = (root.attrib[a]).lower()
            a = a.lower()
            if a == "language":
                language_flag = True
                if ra != "ippcode22":
                    sys.stderr.write("Language flag unsuported\n")
                    exit(31)
                continue
            if a == "name":
                continue
            if a == "description":
                continue
            else:
                print(a)
                sys.stderr.write("Unknown <program> flag\n")
                exit(31)

        if language_flag == False:
            sys.stderr.write("Language flag missing\n")
            exit(31)
    





if __name__ == '__main__':
    main()