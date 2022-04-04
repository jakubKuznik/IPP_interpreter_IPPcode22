from doctest import debug_src
from math import exp
from pickle import NONE
import sys
import re
from unicodedata import name
import xml.etree.ElementTree as ET
import string
from matplotlib.cbook import Stack
import copy

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
        self.__GF = Frame()
        self.__TF : Frame
        self.__LF = []
        self.__stack = [] # use append and pop 
        self.__active_LT : int

    ##
    # pop from stack
    def pop(self):
        self.__stack.pop()
    ##
    # push to stack 
    def push(self, val):
        self.__stack.append(val)

    def get_GF(self):
        return self.__GF
    
    def get_TF(self):
        return self.__TF
    
    def get_FRAME(self, frame):
        if frame.lower() == "gf":
            return self.get_GF()
        elif frame.lower() == "tf":
            return self.get_TF()
        elif frame.lower() == "lf":
            return self.__LF(self.get_active_LT())
        else:
            return NONE

    def get_active_LT(self):
        return self.__active_LT
        
        #try:
        #    return self.__active_LT
        #except:
        #    sys.stderr.write("LF has not been created yet\n")
        #    exit(10)


    def LT_is_empty(self):
        if len(self.__LF) == 0:
            return True
        return False

    ##
    # Copy active LF to TF and delete it 
    def LF_to_TF(self):
        self.__TF = copy.copy(self.__LF[self.get_active_LT()])
        self.remove_LF()

    def set_active_LT(self, val):
        self.__active_LT = val

    def inc_active_LT(self):
        self.__active_LT = self.__active_LT + 1

    def dec_active_LT(self):
        self.__active_LT = self.__active_LT - 1

    def create_TF(self):
        ## todo maybe drop program if double creation
        self.__TF = Frame()
   
    def delete_TF(self):
        del self.__TF
    
    ##
    # Create local frame 
    def create_LF(self):
        self.inc_active_LT()
        if len(self.__LF) == 0:
            self.set_active_LT(0)
            self.__LF[self.get_active_LT()] = copy.copy(self.__TF)
        else:
            self.inc_active_LT()
            self.__LF[self.get_active_LT()] = copy.copy(self.__TF)
        self.delete_TF()

    ##
    # Remove local frame 
    def remove_LF(self):
        if len(self.__LF) == 0:
            error("There is no LF", 32)
        self.__LF.pop(self.get_active_LT())
        self.dec_active_LT()

    ##
    # store variable to active LF 
    def store_var_to_LF(self, name, typ, value):
        var = Variable(name, typ, value)
        try:
            self.__LF[self.get_active_LT()].add_variable(var)
        except:
            error("Frame does not exists ", 32)

    def store_var_to_GF(self, name, typ, value):
        var = Variable(name, typ, value)
        self.__GF.add_variable(var)

    def store_var_to_TF(self, name, typ, value):
        var = Variable(name, typ, value)
        try:
            self.__TF.add_variable(var)
        except:
            error("Frame does not exists ", 32)
            
    
    def store_var(self, frame, name, typ, value):
        if frame.lower() == "lf":
            self.store_var_to_LF(name, typ, value)
        elif frame.lower() == "gf":
            self.store_var_to_GF(name, typ, value)
        elif frame.lower() == "tf":
            self.store_var_to_TF(name, typ, value)
    
    def create_LF(self):
        # first LF hasn't been created yet
        if self.get_First_LT_created() == False:
            self.set_active_LT(0)
        else:
            self.inc_active_LT()
        self.__LF[self.get_active_LT()] = Frame()


    ##
    # Functions that call proper __ins_* function
    def interpret(self, instr):
        self.print_frames()
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
            error("Unknown instruction", 51)

    ##
    # <var> <symb>
    def __ins_move(self, instr):
        self.__control_args(instr, 2)
        typ = instr.get_n_arg(0).get_type()
        if typ != "var":
            error("Move needs variable as first argument", 53)
        typ = instr.get_n_arg(1).get_type()
        
        if typ == "var":
            a, val = self.__control_var(instr.get_n_arg(0))
        else:
            val = self.__control_var(instr.get_n_arg(0).get_content())
        debug(val)
        


        
        debug("move")
    
    ##
    # <var> <symb>
    def __ins_int2char(self, instr):
        self.__control_args(instr, 2)
        debug("i2ch")
    
    ##
    # <var> <symb>
    def __ins_strlen(self, instr):
        self.__control_args(instr, 2)
        debug("strlen")
        
    ##
    # <var> <symb>
    def __ins_type(self, instr):
        self.__control_args(instr, 2)
        debug("type")
    
    ##
    # <var> <symb>
    def __ins_not(self, instr):
        self.__control_args(instr, 2)
        debug("not")
        
    ##
    # <var>
    def __ins_defvar(self, instr):
        self.__control_args(instr, 1)
        self.__control_arg_type(instr, 0, "var")
        frame, var = self.__control_var(instr.get_n_arg(0))
        self.__control_var_exist(var, frame)
        self.store_var(frame, var, "" , "")
        
    ##
    # <var>
    def __ins_pops(self, instr):
        self.__control_args(instr, 1)
        
        debug("pops")
        try:
            set_to = self.pop()
        except:
            error("Stack is empty", 56)

        ## check if variable exists
        frame, val = self.__control_var(instr.get_n_arg(0)) 
        frame = self.get_FRAME(frame)
        if frame.var_exist(val) == False:
            error("Non declared variable access ", 54)
        
        variable = frame.get_variable_index(frame.find_variable_index(val))
        variable.set_name(set_to)
        

    ##
    # <label>
    def __ins_call(self, instr):
        self.__control_args(instr, 1)
        debug("call")
    
    ##
    # <label>
    def __ins_jump(self, instr):
        self.__control_args(instr, 1)
        debug("jump")

    ##
    # <label>
    def __ins_label(self, instr):
        self.__control_args(instr, 1)
        debug("label")
    
    ##
    # <symb>
    def __ins_pushs(self, instr):
        self.__control_args(instr, 1)
        typ = instr.get_n_arg(0).get_type()
        if typ == "var":
            a, val = self.__control_var(instr.get_n_arg(0))
        else: 
            val = self.__control_var(instr.get_n_arg(0).get_content())
        self.push(val)

    ##
    # <symb>
    def __ins_write(self, instr):
        self.__control_args(instr, 1)
        debug("write")
    
    ##
    # <symb>
    def __ins_exit(self, instr):
        self.__control_args(instr, 1)
        debug("exit")

    ##
    # <symb>
    def __ins_dprint(self, instr):
        self.__control_args(instr, 1)
        debug("dprint")

    ##
    # <var> <symb1> <symb2>
    def __ins_add(self, instr):
        self.__control_args(instr, 3)
        debug("add")
    
    ##
    # <var> <symb1> <symb2>
    def __ins_sub(self, instr):
        self.__control_args(instr, 3)
        debug("sub")

    ##
    # <var> <symb1> <symb2>
    def __ins_mul(self, instr):
        self.__control_args(instr, 3)
        debug("mul")
    
    ##
    # <var> <symb1> <symb2>
    def __ins_idiv(self, instr):
        self.__control_args(instr, 3)
        print("idiv")

    ##
    # <var> <symb1> <symb2>
    def __ins_lt(self, instr):
        self.__control_args(instr, 3)
        debug("lt")
    
    ##
    # <var> <symb1> <symb2>
    def __ins_gt(self, instr):
        self.__control_args(instr, 3)
        debug("gt")

    ##
    # <var> <symb1> <symb2>
    def __ins_eq(self, instr):
        self.__control_args(instr, 3)
        debug("eq")
    
    ##
    # <var> <symb1> <symb2>
    def __ins_and(self, instr):
        self.__control_args(instr, 3)
        debug("and")

    ##
    # <var> <symb1> <symb2>
    def __ins_or(self, instr):
        self.__control_args(instr, 3)
        debug("or")
    
    ##
    # <var> <symb1> <symb2>
    def __ins_stri2int(self, instr):
        self.__control_args(instr, 3)
        debug("stri2int")

    ##
    # <var> <symb1> <symb2>
    def __ins_concat(self, instr):
        self.__control_args(instr, 3)
        debug("concat")
    
    ##
    # <var> <symb1> <symb2>
    def __ins_getchar(self, instr):
        self.__control_args(instr, 3)
        debug("getchar")

    ##
    # <var> <symb1> <symb2>
    def __ins_setchar(self, instr):
        self.__control_args(instr, 3)
        debug("setchar")
    
    ##
    # <label> <symb1> <symb2>
    def __ins_jumpifeq(self, instr):
        self.__control_args(instr, 3)
        debug("jumpifeq")

    ##
    # <label> <symb1> <symb2>
    def __ins_jumpifneq(self, instr):
        self.__control_args(instr, 3)
        debug("jumpifneq")
    
    ##
    # <var> <type>
    def __ins_read(self, instr):
        self.__control_args(instr, 2)
        debug("read")

    ##
    # remove TF content and create new one 
    def __ins_createframe(self, instr):
        self.__control_args(instr, 0)
        self.delete_TF()
        self.create_TF() 
    
    ##
    # 
    def __ins_break(self, instr):
        self.__control_args(instr, 0)
        debug("break")
    
    ##
    # 
    def __ins_pushframe(self, instr):
        self.__control_args(instr, 0)
        self.create_LF()
    
    ##
    # 
    def __ins_popframe(self, instr):
        self.__control_args(instr, 0)
        if self.LT_is_empty():
            error("Frame does not exists ", 55)
        self.LF_to_TF()
         
             

    ##
    #
    def __ins_return(self, instr):
        self.__control_args(instr, 0)
        debug("return")
    
    
    ############## CONTROL FUNCTIONS (SEMATIC OR SYNTAX)############
    
    ##
    # from LF@var gets -> [LF, var]
    # TODO transefer /321 to real character 
    def __control_var(self, arg):
        var = arg.get_content()
        var = var.split("@")
        i = ""
        for v in var[1:]:
            i = i + v
        var[1] = i
        if var[0].lower() != "lf" and var[0].lower() != "gf" and var[0].lower() != "tf":
            error("Unexpected frame ", 32)
        return var[0], var[1]

    ##
    # Control if there are number arguments in instruction 
    def __control_args(self, instr, number):
        # instr 
        if len(instr.get_args()) != number:
            error("Not enought instruction arguments", 32)
    ##
    # Check if argument on nth position has expect type 
    def __control_arg_type(self, instr, nth, expect):
        args = instr.get_args()
        arg_type = args[nth].get_type()
        if arg_type.lower() != expect:
            error("Unexpected xml argument", 32)
    
    def __control_var_exist(self, var, frame):
        if frame.lower() == "gf":
            if self.__GF.var_exist(name) == True:
                error("Variable already exist in frame", 52)
        elif frame.lower() == "lf":
            if self.__LF[self.get_active_LT()].var_exist(name) == True:
                error("Variable already exists in frame ",52)
        elif frame.lower() == "tf":
            if self.__TF.var_exist(name) == True:
                error("Variable already exists in frame ", 52)
            
    ################################################################
    
    ##
    # DEBUG FUNCTION 
    def print_frames(self):
        debug("..GF:")
        try:
            for a in self.__GF.get_variables():
                debug("....name:... " + a.get_name(), end=" ")
                debug("....type:... " + a.get_typ(), end=" ")
                debug("....value:... " + str(a.get_value()),)
        except:
            debug("....empty")

        debug("..TF:")
        try:
            for a in self.__TF.get_variables():
                debug("....name:... " + a.get_name(), end=" ")
                debug("....type:... " + a.get_typ(), end=" ")
                debug("....value:... " + str(a.get_value()))
        except:
            debug("....empty")
        
        i = 0
        for frame in self.__LF:
            debug("..LF" + i)
            i+=1
            try:
                for a in frame.get_variables():
                    debug("....name:... " + a.get_name(),end=" ")
                    debug("....type:... " + a.get_typ(),end=" ")
                    debug("....value:..." + str(a.get_value()))
            except:
                debug("....empty")


##
# class that represent one data frame used in GF, TF or in LF
class Frame:
    
    def __init__(self):
        self.__variables = []
    
    def add_variable(self, var):
        self.__variables.append(var)

    def get_variables(self):
        return self.__variables

    def get_variable_index(self, index):
        return self.__variables[index]

    def find_variable_index(self, name):
        i = 0
        for v in self.__variables:
            if v.get_name() == name:
                return i
            i = i + 1
    ##
    # return true if variable exist in frame
    #       else return false 
    def var_exist(self, name):
        for v in self.__variables:
            if v.get_name() == name:
                return True
        return False

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
    
    def get_n_arg(self, n):
        for arg in self.__args:
            if arg.tag == ("arg" + str(n)):
                return arg
        error("Unvalid argument",52)
    
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
            error("To many arguments",10)
        source = False
        input = False
        for a in args[1:]:
            if a == "--help" or a == "-h":
                if len(args) != 2:
                    error("Can't combine help with other params",10)
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
            error("Need --input or --source", 10)
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
            error("File not accesible", 10)
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
            error("Bad xml format", 31)

    ##
    # Valid whole xml and store it  
    def xml_validation(self, root): #<program>
        self.xml_valid_root(root)
        if root.tag.lower() != "program":
            error("Bad xml format expect <program>", 32)
        for child in root:          #<instruction>
            if child.tag.lower() != "instruction":
                error("Bad xml format expect <instruction>", 32)
            inst = self.xml_valid_instr(child)
            inst = Instruction(inst[0], inst[1])
            arg_order = 1
            for ar in child:        #<arg>
                if ar.tag.lower()[0:3] != "arg": 
                    error("Bad xml format expect <arg>",32)  
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
                    error("Not valid instruction", 53)
                opcode_flag = True
                opcode = ia
                continue
            elif a == "order":
                try:
                    ia = int(ia)
                except:
                    error("Bad instruction order",32)
                if ia <= self.get_last_instr():
                    error("Bad instruction order", 32)
                self.set_last_instr(ia)
                order_flag = True
                ord = int(ia)
                continue            
            else:
                error("Bad instruction flag", 32)
        if order_flag == False or opcode_flag == False:
            error("Opcode or order missing", 32)
        return opcode, ord


    ##
    # valid argument of instruction 
    # return arg order, type, content 
    def xml_valid_arg(self, arg, order):
        pat = re.compile('arg' + str(order))
        if not pat.match(arg.tag):
            error("Unvalid arg", 32)

        type_flag = False
        type = ""
        for a in arg.attrib:
            ia = (arg.attrib[a]).lower()
            a = a.lower()
            if a == "type":
                if ia not in valid_types:
                    error("Invalid arg type", 32)
                type_flag = True
                type = ia
                continue
            else:
                error("Invalid arg attribute", 32)

        if type_flag == False:
            error("Missing arg type flag",32)

        return order, type, arg.text


    ##
    # valid root attributes which is <program>
    def xml_valid_root(self, root):
        if root.tag != "program":
            error("Invalid XML root", 32)

        language_flag = False
        for a in root.attrib:
            ra = (root.attrib[a]).lower()
            a = a.lower()
            if a == "language":
                language_flag = True
                if ra != "ippcode22":
                    error("Language flag unsuported", 32)
                continue
            if a == "name":
                continue
            if a == "description":
                continue
            else:
                error("Unknown <program> flag", 31)

        if language_flag == False:
            error("Language flag missing", 31)

def error(string, exit_code):
    sys.stderr.write(string + "\n")
    exit(exit_code)

def debug(string):
    sys.stderr.write(string + "\n")

if __name__ == '__main__':
    main()