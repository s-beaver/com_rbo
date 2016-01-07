set rbo_install_file=..\com_rbo_%DATE:~8,2%%DATE:~3,2%%DATE:~0,2%.zip
if exist %rbo_install_file% del %rbo_install_file%
"C:\Program Files\7-Zip\7z.exe" a -tzip %rbo_install_file% -r *.* -x@create_install_list_file.txt
set rbo_install_file=