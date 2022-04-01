import sys
import re
import xml.etree.ElementTree as ET
import string

from numpy import argsort


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

    ## parse xml using xml library and store instructions 
    root = files.xml_parse()
    files.xml_validation(root)
    
    inst_l = Instruction.get_instructions()
    
    #for i in range(0,3):
    #    print(inst_l[i].get_order())
    #    print(inst_l[i].get_name())
    #    for j in inst_l[i].get_args():
    #        print(j.get_order())
    #        print(j.get_type())
    #        print(j.get_content())
    #    print("\n")

##
# Store one instruction 
# __inst_list class attribute where all instructions are stored 
class Instruction:
    __inst_list = []

    def __init__(self, name, order):
        self.__name  = name
        self.__order = order
        self.__args = []
        Instruction.__inst_list.append(self)

    def get_instructions():
        return Instruction.__inst_list

    def get_name(self):
        return self.__name

    def get_order(self):
        return self.__order
    
    def get_args(self):
        return self.__args
    
    def set_name(self, name):
        self.__name = name
    
    def set_order(self, orde):
        self.__order = orde

    def append_arg(self, arg):
        self.__args.append(arg)    
    

##
# one instrustion argument. 
class Args:

    def __init__(self, order, type, content):
        self.__order   = order
        self.__type    = type
        self.__content = content
    
    def get_order(self):
        return self.__order
    
    def get_type(self):
        return self.__type

    def get_content(self):
        return self.__content
    
    def set_order(self, orde):
        self.__order = orde
    
    def set_type(self, type):
        self.__content = type

    def set_cont(self, cont):
        self.__content = cont
    


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
    # Valid whole xml and store it  
    def xml_validation(self, root): #<program>
        self.xml_valid_root(root)
        for child in root:          #<instruction>
            inst = self.xml_valid_instr(child)
            inst = Instruction(inst[0], inst[1])
            arg_order = 1
            for ar in child:        #<arg>
                arg = self.xml_valid_arg(ar, arg_order)
                arg = Args(arg[0], arg[1], arg[2])
                inst.append_arg(arg)
                arg_order += 1
        
    ##
    # Check if instruction is valid
    # - opcode is valid?
    # - order is ok?
    # if instruction has args chceck if arg has order and type
    #   return instruction name, order 
    def xml_valid_instr(self, inst):
        opcode_flag = False
        order_flag = False
        opcode = ""
        ord = ""
        for a in inst.attrib:
            ia = (inst.attrib[a]).lower()
            a = a.lower()
            if a == "opcode":
                if ia not in valid_instruction:
                    sys.stderr.write("Not valid instruction\n")
                    exit(32)
                opcode_flag = True
                opcode = ia
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
                ord = int(ia)
                continue            
            else:
                sys.stderr.write("Bad instruction flag\n")
                exit(32)
        if order_flag == False or opcode_flag == False:
            sys.stderr.write("Opcode or order missing\n")
            exit(32)
        return opcode, ord


    ##
    # valid argument of instruction 
    # return arg order, type, content 
    def xml_valid_arg(self, arg, order):
        pat = re.compile('arg' + str(order))
        if not pat.match(arg.tag):
            sys.stderr.write("Unvalid arg \n")
            exit(32)

        type_flag = False
        type = ""
        for a in arg.attrib:
            ia = (arg.attrib[a]).lower()
            a = a.lower()
            if a == "type":
                if ia not in valid_types:
                    sys.stderr.write("Invalid arg type \n")
                    exit(32)
                type_flag = True
                type = ia
                continue
            else:
                sys.stderr.write("Invalid arg attribut \n")
                exit(32)

        if type_flag == False:
            sys.stderr.write("Missing arg type flag \n")
            exit(32)

        return order, type, arg.text


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
                sys.stderr.write("Unknown <program> flag\n")
                exit(31)

        if language_flag == False:
            sys.stderr.write("Language flag missing\n")
            exit(31)
    





if __name__ == '__main__':
    main()