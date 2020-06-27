
typedef struct _zend_vm_stack *zend_vm_stack;
typedef struct _zend_ini_entry zend_ini_entry;

struct _zend_executor_globals {
    zval uninitialized_zval;
    zval error_zval;

    /* symbol table cache */
    zend_array *symtable_cache[SYMTABLE_CACHE_SIZE];
    /* Pointer to one past the end of the symtable_cache */
    zend_array **symtable_cache_limit;
    /* Pointer to first unused symtable_cache slot */
    zend_array **symtable_cache_ptr;

    zend_array symbol_table;        /* main symbol table */

    HashTable included_files;    /* files already included */

    JMP_BUF *bailout;

    int error_reporting;
    int exit_status;

    HashTable *function_table;    /* function symbol table */
    HashTable *class_table;        /* class table */
    HashTable *zend_constants;    /* constants table */

    zval          *vm_stack_top;
    zval          *vm_stack_end;
    zend_vm_stack  vm_stack;
    size_t         vm_stack_page_size;

    struct _zend_execute_data *current_execute_data;
    zend_class_entry *fake_scope; /* used to avoid checks accessing properties */

    zend_long precision;

    int ticks_count;

    uint32_t persistent_constants_count;
    uint32_t persistent_functions_count;
    uint32_t persistent_classes_count;

    HashTable *in_autoload;
    zend_function *autoload_func;
    zend_bool full_tables_cleanup;

    /* for extended information support */
    zend_bool no_extensions;

    zend_bool vm_interrupt;
    zend_bool timed_out;
    zend_long hard_timeout;

#ifdef ZEND_WIN32
    OSVERSIONINFOEX windows_version_info;
#endif

    HashTable regular_list;
    HashTable persistent_list;

    int user_error_handler_error_reporting;
    zval user_error_handler;
    zval user_exception_handler;
    zend_stack user_error_handlers_error_reporting;
    zend_stack user_error_handlers;
    zend_stack user_exception_handlers;

    zend_error_handling_t  error_handling;
    zend_class_entry      *exception_class;

    /* timeout support */
    zend_long timeout_seconds;

    int lambda_count;

    HashTable *ini_directives;
    HashTable *modified_ini_directives;
    zend_ini_entry *error_reporting_ini_entry;

    zend_objects_store objects_store;
    zend_object *exception, *prev_exception;
    const zend_op *opline_before_exception;
    zend_op exception_op[3];

    struct _zend_module_entry *current_module;

    zend_bool active;
    zend_uchar flags;

    zend_long assertions;

    uint32_t           ht_iterators_count;     /* number of allocatd slots */
    uint32_t           ht_iterators_used;      /* number of used slots */
    HashTableIterator *ht_iterators;
    HashTableIterator  ht_iterators_slots[16];

    void *saved_fpu_cw_ptr;
#if XPFPA_HAVE_CW
    XPFPA_CW_DATATYPE saved_fpu_cw;
#endif

    zend_function trampoline;
    zend_op       call_trampoline_op;

    zend_bool each_deprecation_thrown;

    HashTable weakrefs;

    zend_bool exception_ignore_args;

    void *reserved[ZEND_MAX_RESERVED_RESOURCES];
};
