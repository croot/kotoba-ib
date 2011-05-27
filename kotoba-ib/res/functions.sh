#!/bin/bash

# Check if exit code not 0 (success) and termitante script.
# $1 - exit code.
# $2 - information about command call place (file, line number).
function check_exitcode {
    if [ $1 -ne 0 ]; then
        echo "Script failed"
        echo "in $2"
        exit 1
    fi
}

# Execute command.
# $1 - command.
# $2 - information about command call place (file, line number).
function execute {
    if [ $DEBUG -eq 1 ]; then
        TABS=""
        echo "Execute command $1"
        for ((i=0; i<${#FUNCNAME[@]}; i++)); do
            echo -n "${TABS}in ${BASH_SOURCE[$i]}:${BASH_LINENO[$i]} "
            echo "${FUNCNAME[$i]}()"
            TABS="$TABS    "
        done
    fi

    echo "Execute command $1"
    eval $1
    check_exitcode $? $2

    return 0
}

# Check if file exists and read permissions granted.
# $1 - full file name.
function is_file_r {
    ret=1

    if [ -e $1 ]; then
        if [ -r $1 ]; then
            ret=0
            if [ $DEBUG -eq 1 ]; then
                TABS=""
                echo "File $1 is ok"
                for ((i=0; i<${#FUNCNAME[@]}; i++)); do
                    echo -n "${TABS}in ${BASH_SOURCE[$i]}:${BASH_LINENO[$i]} "
                    echo "${FUNCNAME[$i]}()"
                    TABS="$TABS    "
                done
            fi
        else
            echo "Have no permission to read file $1"
            ret=1
        fi
    else
        echo "File $1 not exist."
        ret=1
    fi

    return $ret
}

# Check if file exists and read,execute permissions granted.
# $1 - full file name.
function is_file_rx {
    ret=1

    if [ -e $1 ]; then
        if [ -r $1 ]; then
            if [ -x $1 ]; then
                ret=0
                if [ $DEBUG -eq 1 ]; then
                    TABS=""
                    echo "File $1 is ok"
                    for ((i=0; i<${#FUNCNAME[@]}; i++)); do
                        echo -n "${TABS}in ${BASH_SOURCE[$i]}:"
                        echo "${BASH_LINENO[$i]} ${FUNCNAME[$i]}()"
                        TABS="$TABS    "
                    done
                fi
            else
                echo "Have no permission to execute file $1"
                ret=1
            fi
        else
            echo "Have no permission to read file $1"
            ret=1
        fi
    else
        echo "File $1 not exist."
        ret=1
    fi

    return $ret
}

# Check if file exists and read,write,execute permissions granted.
# $1 - full file name.
function is_file_rwx {
    ret=1

    if [ -e $1 ]; then
        if [ -r $1 ]; then
            if [ -w $1 ]; then
                if [ -x $1 ]; then
                    ret=0
                    if [ $DEBUG -eq 1 ]; then
                        TABS=""
                        echo "File $1 is ok"
                        for ((i=0; i<${#FUNCNAME[@]}; i++)); do
                            echo -n "${TABS}in ${BASH_SOURCE[$i]}:"
                            echo "${BASH_LINENO[$i]} ${FUNCNAME[$i]}()"
                            TABS="$TABS    "
                        done
                    fi
                else
                    echo "Have no permission to execute file $1"
                    ret=1
                fi
            else
                echo "Have no permission to write file $1"
                ret=1
            fi
        else
            echo "Have no permission to read file $1"
            ret=1
        fi
    else
        echo "File $1 not exist."
        ret=1
    fi

    return $ret
}
