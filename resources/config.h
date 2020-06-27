
#define XPFPA_HAVE_CW                 1
#define ZEND_MAX_RESERVED_RESOURCES   6
#define SYMTABLE_CACHE_SIZE           32
#define JMP_BUF                       void
#define XPFPA_CW_DATATYPE             unsigned int
#define INTERNAL_FUNCTION_PARAMETERS  zend_execute_data *execute_data, zval *return_value

#ifdef __win64
    #define ZEND_ENABLE_ZVAL_LONG64 1
#endif

#ifdef __windows__
    #define ZEND_WIN32    1

    #define ZEND_API      __declspec(dllimport)
    #define ZEND_FASTCALL __vectorcall
#endif

#ifndef __windows__
    #define ZEND_API extern
    #define ZEND_FASTCALL
#endif
