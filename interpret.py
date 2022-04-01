from ast import arg
from cgi import print_arguments
import sys

from numpy import arange, size


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
    arguments = Arg_parse()


##
#  Parse arguments and store input and source file 
class Arg_parse:

    def __init__(self):
        self.__input_file = ""
        self.__source_file = ""
        self.__parse_arguments(sys.argv)

    def get_input_file(self):
        return self.__input_file    

    def get_source_file(self):
        return self.__source_file    
    
    ##
    # Parse arguments that are stored in args
    # If error or --help exit() program
    # return file_path, command
    def __parse_arguments(self, args):
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
                self.__source_file = self.__string_assemble(temp)
            elif a.startswith("--input="):
                temp = a.split('=')
                self.__input_file = self.__string_assemble(temp)
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









if __name__ == '__main__':
    main()