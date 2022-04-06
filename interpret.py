from dis import Instruction
from pickle import NONE
import sys
import re
import xml.etree.ElementTree as ET
import copy


valid_instruction = [ "move", "int2char", "strlen", "type", "not",
 "defvar", "pops" , "call", "jump", "label", "pushs", "write", 
 "exit", "dprint", "add", "sub", "mul", "idiv", "lt", "gt", "eq",
 "and", "or", "stri2int", "concat", "getchar", "setchar", "jumpifeq",
 "jumpifneq", "read", "createframe", " break", "pushframe", "popframe",
 "return", "div", "int2float", "float2int"]

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
    ## store input file ## input_file  

    input_conte = files.input_store()

    # gets all instructions 
    inter = Interpret(input_conte)
    Instruction.sort_instruction()
    inst_l = Instruction.get_instructions() 
    # interpret instruction one by one
    i = 0
    kill = 0
    while i  < len(inst_l):
        i = inter.interpret(inst_l[i], i)
        i = i + 1
        kill = kill + 1
        if kill == 50:
            error("kill",100)
    
##
# Class for code interpretation 
class Interpret:

    def __init__(self, read_file):
        self.__GF           = Frame()
        self.__TF           : Frame
        self.__LF           = []
        self.__stack        = [] # use append and pop 
        self.__active_LT    : int
        self.__read_file    = read_file
        self.__labels       = []
        self.__instr_stack  = []
    
    def get_labels(self):
        return self.__labels
    
    def find_label(self, name):
        for l in self.get_labels():
            if l.get_name().lower() == name.lower():
                return l
        return None

    def get_read_file(self):
        return self.__read_file

    def store_label(self, label):
        self.__labels.append(label)

    ##
    # pop from instr stack
    def pop_instr(self):
        return self.__instr_stack.pop()
    ##
    # push to instr stack 
    def push_instr(self, val):
        self.__instr_stack.append(val)

    ##
    # get line from read file 
    def get_r_line(self):
        val, self.__read_file = Files.get_first(self.__read_file)
        val = val.strip()
        return val

    ##
    # pop from stack
    def pop(self):
        return self.__stack.pop()
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
        
    def LT_is_empty(self):
        if len(self.__LF) == 0:
            return True
        return False

    ##
    # Copy active LF to TF and delete it 
    def TF_to_LF(self):
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
        if len(self.__LF) == 0:
            self.set_active_LT(0)
            self.__LF.append(copy.deepcopy(self.get_TF()))
            self.__LF[0].__variables = copy.deepcopy(self.get_TF().get_variables())
        else:
            self.inc_active_LT()
            self.__LF.append(copy.deepcopy(self.get_TF()))
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

    def get_var(self, frame, name):
        if frame.lower() == "lf":
            return self.__LF[self.get_active_LT()].get_variable(name)
        elif frame.lower() == "gf":
            return self.__GF.get_variable(name)
        elif frame.lower() == "tf":
            return self.__TF.get_variable(name)
    
    ## 
    # Gets value from <symb> 
    def get_symb_value_from_arg(self, arg):
        typ = arg.get_type()
        if typ == "var":
            frame, name = self.__control_var(arg)
            if self.__control_var_exist(name, frame) == False:
                error("Variable does not exist.", 54)
            return (self.get_var(frame, name)).get_value()
        else:
            frame, name = self.__control_var(arg)
            return name

    def get_variable_from_arg(self, arg):
        typ = arg.get_type()
        if typ != "var":
            error("Expect variable.", 53)
        frame, name = self.__control_var(arg)
        if self.__control_var_exist(name, frame) == False:
            error("Variable does not exist.", 54)
        return self.get_var(frame, name)

    ##
    # Functions that call proper __ins_* function
    # i == instruction index 
    def interpret(self, instr, i):
        
        #sys.stderr.write("\n")
        #sys.stderr.write("..............")
        #sys.stderr.write(instr.get_name())
        #sys.stderr.write("..............")
        #sys.stderr.write("\n")
        #self.print_frames()
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
            i = self.__ins_call(instr,i)
        elif instr.get_name() == "jump":
            i = self.__ins_jump(instr)
        elif instr.get_name() == "label":
            self.__ins_label(instr, i)
        elif instr.get_name() == "pushs":
            self.__ins_pushs(instr)
        elif instr.get_name() == "write":
            self.__ins_write(instr)
        elif instr.get_name() == "exit":
            self.__ins_exit(instr)
        elif instr.get_name() == "dprint":
            self.__ins_dprint(instr)
        elif instr.get_name() == "add":
            self.__int_numeric_command(instr, "add")
        elif instr.get_name() == "sub":
            self.__int_numeric_command(instr, "sub")
        elif instr.get_name() == "mul":
            self.__int_numeric_command(instr, "mul")
        elif instr.get_name() == "idiv":
            self.__int_numeric_command(instr, "idiv")
        elif instr.get_name() == "lt":
            self.__ins_lt_gt(instr, "lt")
        elif instr.get_name() == "gt":
            self.__ins_lt_gt(instr, "gt")
        elif instr.get_name() == "eq":
            self.__ins_eq(instr)
        elif instr.get_name() == "and":
            self.__ins_and_or(instr, "and")
        elif instr.get_name() == "or":
            self.__ins_and_or(instr, "or")
        elif instr.get_name() == "stri2int":
            self.__ins_stri2int(instr)
        elif instr.get_name() == "concat":
            self.__ins_concat(instr)
        elif instr.get_name() == "getchar":
            self.__ins_getchar(instr)
        elif instr.get_name() == "setchar":
            self.__ins_setchar(instr)
        elif instr.get_name() == "jumpifeq":
            i = self.__ins_jumpifeq(instr, i)
        elif instr.get_name() == "jumpifneq":
            i = self.__ins_jumpifneq(instr,i)
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
            i = self.__ins_return(instr)
        elif instr.get_name() == "div":
            self.__ins_div(instr)
        elif instr.get_name() == "int2float":
            self.__ins_int2float(instr)
        elif instr.get_name() == "float2int":
            self.__ins_float2int(instr)
        else:
            error("Unknown instruction", 51)
        return i
    ##
    # <var> <symb>
    def __ins_move(self, instr):
        self.__control_args(instr, 2)

        # <var>
        var1 = self.get_variable_from_arg(instr.get_n_arg(0))

        # <symb>
        value = self.get_symb_value_from_arg(instr.get_n_arg(1))
        var1.set_value(value)
    
    #######################################################
    # <var> <symb>
    def __ins_int2char(self, instr):
        self.__control_args(instr, 2)
        var = self.get_variable_from_arg(instr.get_n_arg(0))
        value = self.get_symb_value_from_arg(instr.get_n_arg(1))
        
        char = ''
        try:
            char = chr(value)
        except:
            error("Invalid int to char",58)
        
        var.set_value(char)
    
    #######################################################
    # <var> <symb>
    def __ins_strlen(self, instr):
        self.__control_args(instr, 2)
        
        var = self.get_variable_from_arg(instr.get_n_arg(0))
        args = instr.get_args()
        
        type1 = args[1].get_type()
        if type1 == "var":
            var1 = self.get_variable_from_arg(args[1])
            var1.set_variable_type()  
            type1 = var1.get_typ()       
        else:
            type1 = args[1].get_type()

        if type1.lower() != "string":
            error("Mismatch types in stri2int",53)

        value1 = self.get_symb_value_from_arg(instr.get_n_arg(1))

        result = len(str(value1))
        var.set_value(result)
        debug("strlen")
    
    #######################################################
    # <var> <symb1> <symb2>
    def __ins_stri2int(self, instr):
        self.__control_args(instr, 3)
        
        var = self.get_variable_from_arg(instr.get_n_arg(0))
        args = instr.get_args()
        
        type1 = args[1].get_type()
        if type1 == "var":
            var1 = self.get_variable_from_arg(args[1])
            var1.set_variable_type()  
            type1 = var1.get_typ()       
        else:
            type1 = args[1].get_type()
        
        type2 = args[2].get_type()
        if type2 == "var":
            var2 = self.get_variable_from_arg(args[2])
            var2.set_variable_type() 
            type2 = var2.get_typ()    
        else:   
            type2 = args[2].get_type()

        if type1.lower() != "string" or type2.lower() != "int":
            error("Mismatch types in stri2int",53)

        value1 = self.get_symb_value_from_arg(instr.get_n_arg(1))
        value2 = self.get_symb_value_from_arg(instr.get_n_arg(2))

        char = '' 
        try:
            char = value1[value2]
            char = ord(char)
        except:
            error("Out of index",58)

        var.set_value(str(char))

    #######################################################
    # <var> <symb1> <symb2>
    def __ins_concat(self, instr):
        self.__control_args(instr, 3)
        
        var = self.get_variable_from_arg(instr.get_n_arg(0))
        args = instr.get_args()
        
        type1 = args[1].get_type()
        if type1 == "var":
            var1 = self.get_variable_from_arg(args[1])
            var1.set_variable_type()  
            type1 = var1.get_typ()       
        else:
            type1 = args[1].get_type()
        
        type2 = args[2].get_type()
        if type2 == "var":
            var2 = self.get_variable_from_arg(args[2])
            var2.set_variable_type() 
            type2 = var2.get_typ()    
        else:   
            type2 = args[2].get_type()

        if type1.lower() != "string" or type2.lower() != "string":
            error("Mismatch types in stri2int",53)

        value1 = self.get_symb_value_from_arg(instr.get_n_arg(1))
        value2 = self.get_symb_value_from_arg(instr.get_n_arg(2))

        result = str(value1) + str(value2)
        var.set_value(result)

        debug("concat")
    
    #######################################################
    # <var> <symb1> <symb2>
    def __ins_getchar(self, instr):
        self.__control_args(instr, 3)
        
        var = self.get_variable_from_arg(instr.get_n_arg(0))
        args = instr.get_args()
        
        type1 = args[1].get_type()
        if type1 == "var":
            var1 = self.get_variable_from_arg(args[1])
            var1.set_variable_type()  
            type1 = var1.get_typ()       
        else:
            type1 = args[1].get_type()
        
        type2 = args[2].get_type()
        if type2 == "var":
            var2 = self.get_variable_from_arg(args[2])
            var2.set_variable_type() 
            type2 = var2.get_typ()    
        else:   
            type2 = args[2].get_type()

        if type1.lower() != "string" or type2.lower() != "int":
            error("Mismatch types in stri2int",53)

        value1 = self.get_symb_value_from_arg(instr.get_n_arg(1))
        value2 = self.get_symb_value_from_arg(instr.get_n_arg(2))

        char = '' 
        try:
            char = value1[value2]
        except:
            error("Out of index",58)

        var.set_value(str(char))

    #######################################################
    # <var> <symb1> <symb2>
    def __ins_setchar(self, instr):
        self.__control_args(instr, 3)
        
        var = self.get_variable_from_arg(instr.get_n_arg(0))
        args = instr.get_args()
        
        type1 = args[1].get_type()
        if type1 == "var":
            var1 = self.get_variable_from_arg(args[1])
            var1.set_variable_type()  
            type1 = var1.get_typ()       
        else:
            type1 = args[1].get_type()
        
        type2 = args[2].get_type()
        if type2 == "var":
            var2 = self.get_variable_from_arg(args[2])
            var2.set_variable_type() 
            type2 = var2.get_typ()    
        else:   
            type2 = args[2].get_type()

        if type1.lower() != "int" or type2.lower() != "string":
            error("Mismatch types in stri2int",53)

        value1 = self.get_symb_value_from_arg(instr.get_n_arg(1))
        value2 = self.get_symb_value_from_arg(instr.get_n_arg(2))

        a = ''
        try:
            a = value2[0]
        except:
            error("Mismatch types in stri2int",53)

        new_valu = var.get_value()
        try:
            new_valu[value1] = a
        except:
            error("Mismatch types in stri2int",53)

        var.set_value(new_valu)
    
    #######################################################
    # <symb>
    def __ins_dprint(self, instr):
        self.__control_args(instr, 1)
        value1 = self.get_symb_value_from_arg(instr.get_n_arg(0))
        sys.stderr.write(value1)
        

    #######################################################
    # 
    def __ins_break(self, instr):
        self.__control_args(instr, 0)
        self.print_frames()

    ##
    # <var> <symb>
    def __ins_type(self, instr):
        self.__control_args(instr, 2)
        var = self.get_variable_from_arg(instr.get_n_arg(0))
        value = self.get_symb_value_from_arg(instr.get_n_arg(1))
        value = str(value)
        if value == "":
            var.set_value("NONETYPE")
        elif value == "true" or value == "false":
            var.set_value("bool")
        elif value == "nil":
            var.set_value("nil")
        elif re.match(pattern_int, value):
            var.set_value("int")
        elif re.match(pattern_float, value):
            var.set_value("float")
        else:
            var.set_value("string")
    
    ##
    # <var> <symb>
    def __ins_not(self, instr):
        self.__control_args(instr, 2)
        var = self.get_variable_from_arg(instr.get_n_arg(0))
        args = instr.get_args()
        
        type1 = args[1].get_type()
        if type1 == "var":
            var1 = self.get_variable_from_arg(args[1])
            var1.set_variable_type()  
            type1 = var1.get_typ()       
        else:
            type1 = args[1].get_type()
        
        if type1.lower() != "bool":
            error("Bad operands type", 53)

        value1 = self.get_symb_value_from_arg(instr.get_n_arg(1))
        if value1.lower() != "true" or value1.lower() != "false":
            error("Bad type", 53)

        var.set_value(str(not value1))
        
    ##
    # <var>
    def __ins_defvar(self, instr):
        self.__control_args(instr, 1)
        self.__control_arg_type(instr, 0, "var")
        frame, var = self.__control_var(instr.get_n_arg(0))
        if self.__control_var_exist(var, frame) == True:
            error("Variable already exist in frame", 52)
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
        var = self.get_variable_from_arg(instr.get_n_arg(0)) 
        var.set_value(set_to)
        
    ##
    # <label>
    def __ins_call(self, instr, i):
        self.__control_args(instr, 1)
        self.push_instr(i)
        return self.jump(instr)
    
    ##
    # <label>
    def jump(self, instr):
        name = self.get_symb_value_from_arg(instr.get_n_arg(0))
        label = self.find_label(name)
        if label == None:
            inst_l = Instruction.get_instructions()
            for i in range(0, len(inst_l)):
                if inst_l[i].get_name().lower() == "label":
                    self.__control_args(inst_l[i], 1)
                    self.__control_arg_type(inst_l[i], 0, "label")
                    name2 = self.get_symb_value_from_arg(inst_l[i].get_n_arg(0))
                    if name.lower() == name2.lower():
                        return i
            error("Non existing label ",52)
        else:
            return label.get_inst_index()

    ##
    # <label>
    def __ins_jump(self, instr):
        self.__control_args(instr, 1)
        self.__control_arg_type(instr, 0, "label")

        return self.jump(instr)

    ##
    # <label>
    def __ins_label(self, instr, i):
        self.__control_args(instr, 1)
        self.__control_arg_type(instr, 0, "label")
        name = self.get_symb_value_from_arg(instr.get_n_arg(0))
        if self.find_label(name) != None:
            error("Duplicit label declaration",52)
        label = Label(name, i)
        self.store_label(label)
    
    ##
    # <symb>
    def __ins_pushs(self, instr):
        self.__control_args(instr, 1)
        val = self.get_symb_value_from_arg(instr.get_n_arg(0))
        self.push(val)

    ##
    # <symb>
    # nil is empty string 
    def __ins_write(self, instr):
        self.__control_args(instr, 1)
        typ = instr.get_n_arg(0).get_type()
        if typ == "var":
            ## check if variable exists
            var1 = self.get_variable_from_arg(instr.get_n_arg(0))
            val = str(var1.get_value())
            val = replace_non_print(val)
            if val == "nil":
                print("", end='')
            elif val == "":
                print("", end=' ')
            elif val == "NONETYPE":
                print("", end='')
            else:
                print(val, end='')
        else:
            val = self.get_symb_value_from_arg(instr.get_n_arg(0))
            val = replace_non_print(val)
            if val.lower() == "nil":
                print("", end='')
            else:
                print(val, end='')

    ##
    # <symb>
    def __ins_exit(self, instr):
        self.__control_args(instr, 1)
        value = self.get_symb_value_from_arg(instr.get_n_arg(0))
        try:
            value = int(value)
        except:
            error("Exit value not valid",57)
        if value < 0 or value > 49:
            error("Exit value not valid",57)
        exit(value)


    ##
    # ADD SUB MUL IDIV
    def __int_numeric_command(self, instr, command):
        if instr.get_n_arg(1).get_type() != "var":
            if instr.get_n_arg(1).get_type() != "int":
                error("unsuported type ", 53)
        if instr.get_n_arg(2).get_type() != "var":
            if instr.get_n_arg(2).get_type() != "int":
                error("unsuported type ", 53)
        
        self.__control_args(instr, 3)
        ## <var>
        var1 = self.get_variable_from_arg(instr.get_n_arg(0))
        ## <symb1>
        val1 = self.get_symb_value_from_arg(instr.get_n_arg(1))
        ## <symb2>
        val2 = self.get_symb_value_from_arg(instr.get_n_arg(2))
        if not (re.match(pattern_int, str(val1))) or not (re.match(pattern_int, str(val2))):
            error("instruction needs numbers", 56)
        if command == "add":
            var1.set_value(int(val1) + int(val2))
        elif command == "sub":
            var1.set_value(int(val1) - int(val2))
        elif command == "idiv":
            if int(val2) == 0:
                error("zero div", 57)
            var1.set_value(int(val1) // int(val2))
        elif command == "mul":
            var1.set_value(int(val1) * int(val2))
    

    ##
    # <var> <symb1> <symb2>
    def __ins_lt_gt(self, instr, operation):
        self.__control_args(instr, 3)
        
        var = self.get_variable_from_arg(instr.get_n_arg(0))
        args = instr.get_args()
        
        type1 = args[1].get_type()
        if type1 == "var":
            var1 = self.get_variable_from_arg(args[1])
            var1.set_variable_type()  
            type1 = var1.get_typ()       
        else:
            type1 = args[1].get_type()
        
        type2 = args[2].get_type()
        if type2 == "var":
            var2 = self.get_variable_from_arg(args[2])
            var2.set_variable_type() 
            type2 = var2.get_typ()    
        else:   
            type2 = args[2].get_type()

        if type1.lower() != type2.lower():
            error("mismatch types",56)
        elif type1.lower() == "nil" or type2.lower() == "nil":
            error("mismatch types",56)

        
        value1 = self.get_symb_value_from_arg(instr.get_n_arg(1))
        value2 = self.get_symb_value_from_arg(instr.get_n_arg(2))
        
        # false is smaller then true  
        if operation == "lt":    # <symb1> < <symb2>
            
            if type1.lower() == "bool":
                if value1.lower() == "fals" and value2.lower() == "true":
                    var.set_value("true")
                else:
                    var.set_value("false")
            else:
                var.set_value(str(value1 < value1))
        elif operation == "gt":  # <symb1> > <symb2>
            if type1.lower() == "bool":
                if value1.lower() == "true" and value2.lower() == "false":
                    var.set_value("true")
                else:
                    var.set_value("false")
            else:
                var.set_value(str(value1 > value1))


    ##
    # <var> <symb1> <symb2>
    def __ins_eq(self, instr):
        self.__control_args(instr, 3)
        var = self.get_variable_from_arg(instr.get_n_arg(0))
        args = instr.get_args()
        
        type1 = args[1].get_type()
        if type1 == "var":
            var1 = self.get_variable_from_arg(args[1])
            var1.set_variable_type()  
            type1 = var1.get_typ()       
        else:
            type1 = args[1].get_type()
        
        type2 = args[2].get_type()
        if type2 == "var":
            var2 = self.get_variable_from_arg(args[2])
            var2.set_variable_type() 
            type2 = var2.get_typ()    
        else:   
            type2 = args[2].get_type()


        if type1.lower() != type2.lower():
            if type1.lower() == "nil" or type2.lower() == "nil":
                if type1.lower() == type2.lower():
                    var.set_value("true")
                else:
                    var.set_value("true")
            else:
                error("mismatch types",53) 
        
        value1 = self.get_symb_value_from_arg(instr.get_n_arg(1))
        value2 = self.get_symb_value_from_arg(instr.get_n_arg(2))
        
        if value1 == value2:
            var.set_value("true")
        else:
            var.set_value("false")

    ##
    # <var> <symb1> <symb2>
    def __ins_and_or(self, instr, operation):
        self.__control_args(instr, 3)
        var = self.get_variable_from_arg(instr.get_n_arg(0))
        args = instr.get_args()
        
        type1 = args[1].get_type()
        if type1 == "var":
            var1 = self.get_variable_from_arg(args[1])
            var1.set_variable_type()  
            type1 = var1.get_typ()       
        else:
            type1 = args[1].get_type()
        
        type2 = args[2].get_type()
        if type2 == "var":
            var2 = self.get_variable_from_arg(args[2])
            var2.set_variable_type() 
            type2 = var2.get_typ()    
        else:   
            type2 = args[2].get_type()

        if type1.lower() != "bool" or type2.lower() != "bool":
            error("Bad operands type", 53)
        
        value1 = self.get_symb_value_from_arg(instr.get_n_arg(1))
        value2 = self.get_symb_value_from_arg(instr.get_n_arg(2))

        if value1.lower() != "true" and value1.lower() != "false":
            error("Bad type", 53)
        if value2.lower() != "true" and value2.lower() != "false":
            error("Bad type", 53)


        if operation == "and":
            if value1 == "true" and value2 == "true":
                var.set_value(str("true"))
            else:
                var.set_value(str("false"))
        elif operation == "or":
            if value1 == "false" and value2 == "false":
                var.set_value(str("false"))
            else:
                var.set_value(str("true"))

    
    ##
    # <label> <symb1> <symb2>
    def __ins_jumpifeq(self, instr, i):
        self.__control_args(instr, 3)
        args = instr.get_args()
        
        type1 = args[1].get_type()
        if type1 == "var":
            var1 = self.get_variable_from_arg(args[1])
            var1.set_variable_type()  
            type1 = var1.get_typ()       
        else:
            type1 = args[1].get_type()
        
        type2 = args[2].get_type()
        if type2 == "var":
            var2 = self.get_variable_from_arg(args[2])
            var2.set_variable_type() 
            type2 = var2.get_typ()    
        else:   
            type2 = args[2].get_type()

        value1 = self.get_symb_value_from_arg(instr.get_n_arg(1))
        value2 = self.get_symb_value_from_arg(instr.get_n_arg(2))
        
        if type1 == "nil" or type2 == "nil":
            return self.jump(instr)
        elif type1 != type2:
            error("Jumpifeq Type mismatch", 53)

        if str(value1) == str(value2):
            return self.jump(instr)
        return i

    ##
    # <label> <symb1> <symb2>
    def __ins_jumpifneq(self, instr, i):
        self.__control_args(instr, 3)
        args = instr.get_args()
        
        type1 = args[1].get_type()
        if type1 == "var":
            var1 = self.get_variable_from_arg(args[1])
            var1.set_variable_type()  
            type1 = var1.get_typ()       
        else:
            type1 = args[1].get_type()
        
        type2 = args[2].get_type()
        if type2 == "var":
            var2 = self.get_variable_from_arg(args[2])
            var2.set_variable_type() 
            type2 = var2.get_typ()    
        else:   
            type2 = args[2].get_type()

        value1 = self.get_symb_value_from_arg(instr.get_n_arg(1))
        value2 = self.get_symb_value_from_arg(instr.get_n_arg(2))
        
        if type1 == "nil" or type2 == "nil":
            return self.jump(instr)
        elif type1 != type2:
            error("Jumpifeq Type mismatch", 53)

        if str(value1) != str(value2):
            return self.jump(instr)
        return i

    ##
    # <var> <type>
    def __ins_read(self, instr):
        self.__control_args(instr, 2)

        var = self.get_variable_from_arg(instr.get_n_arg(0))
        typ = self.get_symb_value_from_arg(instr.get_n_arg(1))
         
        try:
            val = self.get_r_line() 
        except:
            typ = "nil"
            val = "nil"

        if typ == "nil":
            if val != "nil":
                error("Mismatch type",55)
        elif typ == "int":
            if not re.match(pattern_int, val):
                error("Mismatch type",55)
            val = val
        elif typ == "string":
            val = val
        elif typ == "bool":
            if val.lower() == "true":
                val = "true"
            else:
                val = "false"
        else:
            error("Mismatch type",55)

        var.set_value(val)
        var.set_typ(typ)

    ##
    # remove TF content and create new one 
    def __ins_createframe(self, instr):
        self.__control_args(instr, 0)
        try:
            self.delete_TF()
        except:
            pass
        self.create_TF() 
    
    
    ##
    # 
    def __ins_pushframe(self, instr):
        self.__control_args(instr, 0)
        
        try:
            self.get_TF()
        except:
            error("Frame does not exist", 55)
        self.create_LF()

    ##
    # 
    def __ins_popframe(self, instr):
        self.__control_args(instr, 0)
        if self.LT_is_empty():
            error("Frame does not exists ", 55)
        self.TF_to_LF()
    
    ##
    #
    def __ins_return(self, instr):
        self.__control_args(instr, 0)
        try:
            i = self.pop_instr()
        except:
            error("Empty instruction stack", 56)
        return i
    
    
    ############## CONTROL FUNCTIONS (SEMATIC OR SYNTAX)############
    
    ##
    # from LF@var gets -> [LF, var]
    # TODO transefer /321 to real character 
    def __control_var(self, arg):
        var = arg.get_content()
        var = var.split("@")
        i = ""
        if len(var) == 1:
            return "", var[0]
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
    
    ## returns true if exist 
    def __control_var_exist(self, name, frame):
        if frame.lower() == "gf":
            try:
                self.get_GF()
            except:
                error("Frame does not exist",55) 
            if self.__GF.var_exist(name) == True:
                return True
        elif frame.lower() == "lf":
            try:
                self.__LF[self.get_active_LT()]
            except:
                error("Frame does not exist",55) 
            if self.__LF[self.get_active_LT()].var_exist(name) == True:
                return True
        elif frame.lower() == "tf":
            try:
                self.get_TF()
            except:
                error("Frame does not exist",55) 
            if self.__TF.var_exist(name) == True:
                return True
        return False
            
    ################################################################
    
    ##
    # DEBUG FUNCTION 
    def print_frames(self):
        
        debug("\n..GF:")
        try:
            for a in self.__GF.get_variables():
                debug("      name:... " + str(a.get_name()))
                debug("      type:... " + str(a.get_typ()))
                debug("      value:... " + str(a.get_value()))
        except:
            debug("....empty")

        debug("..TF:")
        try:
            for a in self.__TF.get_variables():
                debug("      name:... " + str(a.get_name()))
                debug("      type:... " + str(a.get_typ()))
                debug("      value:... " + str(a.get_value()))
        except:
            debug("....empty")
        
        i = 0
        for frame in self.__LF:
            debug("..LF" + str(i))
            i+=1
            try:
                for a in frame.get_variables():
                    debug("      name:... " + str(a.get_name()))
                    debug("      type:... " + str(a.get_typ()))
                    debug("      value:... " + str(a.get_value()))
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
    
    def get_variable(self, name):
        index = self.find_variable_index(name)
        return self.get_variable_index(index)

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
        self.__value = value #nil == nil 
                             #true, false    
    
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

    def set_variable_type(self):
        value = self.get_value()
        if value == "":
            self.set_typ("NONETYPE")
        elif value == "true" or value == "false":
            self.set_typ("bool")
        elif value == "nil":
            self.set_typ("nil")
        elif re.match(pattern_int, str(value)):
            self.set_typ("int")
        elif re.match(pattern_float, str(value)):
            self.set_typ("float")
        else:
            self.set_typ("string")

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

    def sort_instruction():
        arr = {}
        for instr in Instruction.get_instructions():
            index = instr.get_order()
            arr[index] = instr
        list = []
        for key in sorted(arr.keys()):
            list.append(arr[key])
        
        Instruction.__inst_list = list

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
        
    def sort_args(self):
        ar = []
        for i in range(1,6):
            for a in self.__args:
                if int(a.get_order()) == int(i):
                    ar.append(a)
        self.__args = ar

    
    def get_n_arg(self, n):
        for arg in self.__args:
            if arg.get_tag() == ("arg" + str(n+1)):
                return arg
        error("Unvalid argument",52)
    
##
# Label 
class Label:

    def __init__(self, name, inst_index):
        self.__name = name
        self.__inst_index = inst_index

    def get_name(self):
        return self.__name
    
    def set_name(self, name):
        self.__name = name
    
    def set_inst_index(self, index):
        self.__inst_index = index
    
    def get_inst_index(self):
        return self.__inst_index
    
##
# one instrustion argument. 
class Args:

    def __init__(self, order, typ, content, tag):
        self.__order   = order     # 1,2,3 .... 
        self.__typ    = typ        # var, string .... 
        self.__content = content   # whatever 
        self.__tag     = tag       #<arg1>
    
    def get_tag(self):
        return self.__tag

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

    def get_first(list):
        val = list[0]
        list = list[1:]
        return val, list

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
            for ar in child:        #<arg>
                arg = self.xml_valid_arg(ar)
                arg = Args(arg[0], arg[1], arg[2], ar.tag.lower())
                inst.append_arg(arg)
            inst.sort_args()

            i = 1
            for a in inst.get_args():
                if int(a.get_order()) != int(i):
                    error("Bad xml format expect <instruction>", 32)
                i=i+1


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
                    error("Not valid instruction", 32)
                opcode_flag = True
                opcode = ia
                continue
            elif a == "order":
                try:
                    ia = int(ia)
                except:
                    error("Bad instruction order",32)
                if ia <= 0:
                    error("Bad instruction order",32)
                for i in Instruction.get_instructions():
                    if i.get_order() == ia:
                        error("Bad instruction order",32)

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
    def xml_valid_arg(self, arg):
        pat = re.compile('arg' + "1")
        pat2 = re.compile('arg' + "2")
        pat3 = re.compile('arg' + "3")
        if not pat.match(arg.tag):
            if not pat2.match(arg.tag):
                if not pat3.match(arg.tag):
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
        order = arg.tag.replace("arg", "")
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

def replace_non_print(val):
    val = val.replace('\\032', " ")
    return val

pattern_int = '^[-+]?[0-9]+$'
pattern_float = '[+-]?[0-9]+\.[0-9]+'


if __name__ == '__main__':
    main()