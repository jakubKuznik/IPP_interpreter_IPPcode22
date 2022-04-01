from importlib.metadata import files
import sys

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
    
    ##
    # check if file exist
    def file_exist(self, file):
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





if __name__ == '__main__':
    main()