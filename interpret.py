import sys
import re
import xml.etree.ElementTree as ET
import string
from matplotlib.cbook import Stack

from numpy import argsort


valid_instruction = [ "move", "int2char", "strlen", "type", "not",
 "defvar", "pops" , "call", "jump", "label", "pushs", "write", 
 "exit", "dprint", "add", "sub", "mul", "idiv", "lt", "gt", "eq",
 "and", "or", "stri2int", "concat", "getchar", "setchar", "jumpifeq",
 "jumpifneq", "read", "createframe", " break", "pushframe", "popframe"
 "return"]

valid_types = ["int", "bool", "string", "nil", "label", "type", "var", "float"]


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
    arg = Arg_parse(True, "", "")
    
    ## files is object where program valid files and reads from them
    files = Files(False, (arg.get_in_file()), (arg.get_so_file()))
    files.files_exists()

    ## parse xml using xml library and store instructions 
    root = files.xml_parse()
    files.xml_validation(root)
    ## store input file 
    input_conte = files.input_store()
    
    # gets all instructions 
    inter = Interpret()
    inst_l = Instruction.get_instructions() 
    # interpret instruction one by one 
    for instr in inst_l:
        inter.interpret(instr)
    
##
# Class for code interpretation 
class Interpret:

    def __init__(self):
        self.__GF : Frame
        self.__TF : Frame
        self.__LF = []
        self.__stack = [] # use append and pop 

    ##
    # pop from stack
    def pop(self):
        self.__stack.pop()
    ##
    # push to stack 
    def push(self, val):
        self.__stack.append(val)



    ##
    # Functions that call proper __ins_* function
    def interpret(self, instr):
        if instr.get_name() == "move":
            self.__ins_move(instr)
        elif instr.get_name() == "int2char":
            self.__ins_int2char(instr)
        elif instr.get_name() == "strlen":
            self.__ins_strlen(instr)
        elif instr.get_name() == "type":
            self.__ins_type(instr)
        elif instr.get_name() == "not":
            self.__ins_not(instr)
        elif instr.get_name() == "defvar":
            self.__ins_defvar(instr)
        elif instr.get_name() == "pops":
            self.__ins_pops(instr)
        elif instr.get_name() == "call":
            self.__ins_call(instr)
        elif instr.get_name() == "jump":
            self.__ins_jump(instr)
        elif instr.get_name() == "label":
            self.__ins_label(instr)
        elif instr.get_name() == "pushs":
            self.__ins_pushs(instr)
        elif instr.get_name() == "write":
            self.__ins_write(instr)
        elif instr.get_name() == "exit":
            self.__ins_exit(instr)
        elif instr.get_name() == "dprint":
            self.__ins_dprint(instr)
        elif instr.get_name() == "add":
            self.__ins_add(instr)
        elif instr.get_name() == "sub":
            self.__ins_sub(instr)
        elif instr.get_name() == "mul":
            self.__ins_mul(instr)
        elif instr.get_name() == "idiv":
            self.__ins_idiv(instr)
        elif instr.get_name() == "lt":
            self.__ins_lt(instr)
        elif instr.get_name() == "gt":
            self.__ins_gt(instr)
        elif instr.get_name() == "eq":
            self.__ins_eq(instr)
        elif instr.get_name() == "and":
            self.__ins_and(instr)
        elif instr.get_name() == "or":
            self.__ins_or(instr)
        elif instr.get_name() == "stri2int":
            self.__ins_stri2int(instr)
        elif instr.get_name() == "concat":
            self.__ins_concat(instr)
        elif instr.get_name() == "getchar":
            self.__ins_getchar(instr)
        elif instr.get_name() == "setchar":
            self.__ins_setchar(instr)
        elif instr.get_name() == "jumpifneq":
            self.__ins_jumpifeq(instr)
        elif instr.get_name() == "read":
            self.__ins_read(instr)
        elif instr.get_name() == "createframe":
            self.__ins_createframe(instr)
        elif instr.get_name() == "break":
            self.__ins_break(instr)
        elif instr.get_name() == "pushframe":
            self.__ins_pushframe(instr)
        elif instr.get_name() == "popframe":
            self.__ins_popframe(instr)
        elif instr.get_name() == "return":
            self.__ins_return(instr)
        else:
            sys.stderr.write("Unknown instruction\n")
            exit(51)
    
    ##
    # Control if there are number arguments in instruction 
    def __control_args(self, instr, number):
        # instr 
        if len(instr.get_args()) != number:
            sys.stderr.write("Not enought instruction arguments\n")
            exit(32)


    ##
    # <var> <symb>
    def __ins_move(self, instr):
        self.__control_args(instr, 2)
        print("move")
    
    ##
    # <var> <symb>
    def __ins_int2char(self, instr):
        self.__control_args(instr, 2)
        print("i2ch")
    
    ##
    # <var> <symb>
    def __ins_strlen(self, instr):
        self.__control_args(instr, 2)
        print("strlen")
        
    ##
    # <var> <symb>
    def __ins_type(self, instr):
        self.__control_args(instr, 2)
        print("type")
    
    ##
    # <var> <symb>
    def __ins_not(self, instr):
        self.__control_args(instr, 2)
        print("not")
        
    ##
    # <var>
    def __ins_defvar(self, instr):
        self.__control_args(instr, 1)
        print(instr.get_name())
        print(len(instr.get_args()))
    
    ##
    # <var>
    def __ins_pops(self, instr):
        self.__control_args(instr, 1)
        print("pops")

    ##
    # <label>
    def __ins_call(self, instr):
        self.__control_args(instr, 1)
        print("call")
    
    ##
    # <label>
    def __ins_jump(self, instr):
        self.__control_args(instr, 1)
        print("jump")

    ##
    # <label>
    def __ins_label(self, instr):
        self.__control_args(instr, 1)
        print("label")
    
    ##
    # <symb>
    def __ins_pushs(self, instr):
        self.__control_args(instr, 1)
        print("pushs")

    ##
    # <symb>
    def __ins_write(self, instr):
        self.__control_args(instr, 1)
        print("write")
    
    ##
    # <symb>
    def __ins_exit(self, instr):
        self.__control_args(instr, 1)
        print("exit")

    ##
    # <symb>
    def __ins_dprint(self, instr):
        self.__control_args(instr, 1)
        print("dprint")

    ##
    # <var> <symb1> <symb2>
    def __ins_add(self, instr):
        self.__control_args(instr, 3)
        print("add")
    
    ##
    # <var> <symb1> <symb2>
    def __ins_sub(self, instr):
        self.__control_args(instr, 3)
        print("sub")

    ##
    # <var> <symb1> <symb2>
    def __ins_mul(self, instr):
        self.__control_args(instr, 3)
        print("mul")
    
    ##
    # <var> <symb1> <symb2>
    def __ins_idiv(self, instr):
        self.__control_args(instr, 3)
        print("idiv")

    ##
    # <var> <symb1> <symb2>
    def __ins_lt(self, instr):
        self.__control_args(instr, 3)
        print("lt")
    
    ##
    # <var> <symb1> <symb2>
    def __ins_gt(self, instr):
        self.__control_args(instr, 3)
        print("gt")

    ##
    # <var> <symb1> <symb2>
    def __ins_eq(self, instr):
        self.__control_args(instr, 3)
        print("eq")
    
    ##
    # <var> <symb1> <symb2>
    def __ins_and(self, instr):
        self.__control_args(instr, 3)
        print("and")

    ##
    # <var> <symb1> <symb2>
    def __ins_or(self, instr):
        self.__control_args(instr, 3)
        print("or")
    
    ##
    # <var> <symb1> <symb2>
    def __ins_stri2int(self, instr):
        self.__control_args(instr, 3)
        print("stri2int")

    ##
    # <var> <symb1> <symb2>
    def __ins_concat(self, instr):
        self.__control_args(instr, 3)
        print("concat")
    
    ##
    # <var> <symb1> <symb2>
    def __ins_getchar(self, instr):
        self.__control_args(instr, 3)
        print("getchar")

    ##
    # <var> <symb1> <symb2>
    def __ins_setchar(self, instr):
        self.__control_args(instr, 3)
        print("setchar")
    
    ##
    # <label> <symb1> <symb2>
    def __ins_jumpifeq(self, instr):
        self.__control_args(instr, 3)
        print("jumpifeq")

    ##
    # <label> <symb1> <symb2>
    def __ins_jumpifneq(self, instr):
        self.__control_args(instr, 3)
        print("jumpifneq")
    
    ##
    # <var> <type>
    def __ins_read(self, instr):
        self.__control_args(instr, 2)
        print("read")

    ##
    # 
    def __ins_createframe(self, instr):
        self.__control_args(instr, 0)
        print("createframe")
    
    ##
    # 
    def __ins_break(self, instr):
        self.__control_args(instr, 0)
        print("break")
    
    ##
    # 
    def __ins_pushframe(self, instr):
        self.__control_args(instr, 0)
        print("pushframe")
    
    ##
    # 
    def __ins_popframe(self, instr):
        self.__control_args(instr, 0)
        print("popframe")
    
    ##
    #
    def __ins_return(self, instr):
        self.__control_args(instr, 0)
        print("return")

##
# class that represent one data frame used in GF, TF or in LF
class Frame:
    
    def __init__(self):
        self.__varables = []
    
    def add_variable(self, var):
        self.__varables.append(var)

##
# one variable or constant 
class Variable:
    def __init__(self, name, typ, value):
        self.__name = name
        self.__typ  = typ
        self.__value = value
    
    def get_name(self):
        return self.__name
    def get_typ(self):
        return self.__typ
    def get_value(self):
        return self.__value
    
    def set_name(self, val):
        self.__name = val
    def set_typ(self, val):
        self.__typ = val
    def set_value(self, val):
        self.__value = val


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

    def __init__(self, order, typ, content):
        self.__order   = order
        self.__typ    = typ
        self.__content = content
    
    def get_order(self):
        return self.__order
    
    def get_type(self):
        return self.__typ

    def get_content(self):
        return self.__content
    
    def set_order(self, orde):
        self.__order = orde
    
    def set_type(self, typ):
        self.__content = typ

    def set_cont(self, cont):
        self.__content = cont
    
##
#  Parse arguments and store input and source file 
class Arg_parse:
    
    def __init__(self, parsing, in_f, so_f):
        if parsing == True:
            self.__input_file = ""
            self.__source_file = ""
            self.__parse_arguments(sys.argv)
        else:
            self.__input_file = in_f
            self.__source_file = so_f

    def get_in_file(self):
        return self.__input_file    

    def get_so_file(self):
        return self.__source_file   

    def set_in_file(self, val):
        self.__input_file = val
    
    def set_so_file(self, val):
        self.__source_file = val
    
    ##
    # Parse arguments that are stored in args
    # If error or --help exit() program
    # return file_path, command
    def __parse_arguments(self, args):
        if len(args) > 3:
            sys.stderr.write("To many arguments.\n")
            exit(10)
        source = False
        input = False
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
                source = True
            elif a.startswith("--input="):
                temp = a.split('=')
                self.set_in_file(self.__string_assemble(temp))
                input = True
            else:
                sys.stderr.write("Unknown \n")
                exit(10)
        if input == False and source == False:
            sys.stderr.write("Need --input or --source\n")
            exit(10)
        elif input == True and source == False:
            self.set_so_file(sys.stdin)
        elif input == False and source == True:
            self.set_in_file(sys.stdin)



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

    def __init__(self, parsing, in_f, so_f):
        super().__init__(parsing, in_f, so_f)
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
        if self.get_in_file() != sys.stdin:
            self.file_exist(self.get_in_file())
        if self.get_so_file() != sys.stdin:
            self.file_exist(self.get_so_file())

    ##
    # store input file 
    def input_store(self):
        if self.get_in_file() != sys.stdin:
            f = open(self.get_in_file())
            Lines = f.readlines()
            array = []
            for l in Lines:
                array.append(l)
            f.close()
            return array

        Lines = self.get_in_file().readlines()
        array = []
        for l in Lines:
            array.append(l)
        return array

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