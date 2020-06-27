<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Observer\Zend;

use FFI\CData;
use Serafim\Observer\Zend;

/**
 * <code>
 * struct _zend_executor_globals {
 *      zval                        uninitialized_zval;
 *      zval                        error_zval;
 *      zend_array                 *symtable_cache[SYMTABLE_CACHE_SIZE];
 *      zend_array                **symtable_cache_limit;
 *      zend_array                **symtable_cache_ptr;
 *      zend_array                  symbol_table;
 *      HashTable                   included_files;
 *      JMP_BUF                     *bailout;
 *      int                         error_reporting;
 *      int                         exit_status;
 *      HashTable                  *function_table;
 *      HashTable                  *class_table;
 *      HashTable                  *zend_constants;
 *      zval                       *vm_stack_top;
 *      zval                       *vm_stack_end;
 *      zend_vm_stack               vm_stack;
 *      size_t                      vm_stack_page_size;
 *      struct _zend_execute_data  *current_execute_data;
 *      zend_class_entry           *fake_scope;
 *      zend_long                   precision;
 *      int                         ticks_count;
 *      uint32_t                    persistent_constants_count;
 *      uint32_t                    persistent_functions_count;
 *      uint32_t                    persistent_classes_count;
 *      HashTable                  *in_autoload;
 *      zend_function              *autoload_func;
 *      zend_bool                   full_tables_cleanup;
 *      zend_bool                   no_extensions;
 *      zend_bool                   vm_interrupt;
 *      zend_bool                   timed_out;
 *      zend_long                   hard_timeout;
 *      #ifdef ZEND_WIN32
 *          OSVERSIONINFOEX         windows_version_info;
 *      #endif
 *      HashTable                   regular_list;
 *      HashTable                   persistent_list;
 *      int                         user_error_handler_error_reporting;
 *      zval                        user_error_handler;
 *      zval                        user_exception_handler;
 *      zend_stack                  user_error_handlers_error_reporting;
 *      zend_stack                  user_error_handlers;
 *      zend_stack                  user_exception_handlers;
 *      zend_error_handling_t       error_handling;
 *      zend_class_entry           *exception_class;
 *      zend_long                   timeout_seconds;
 *      int                         lambda_count;
 *      HashTable                  *ini_directives;
 *      HashTable                  *modified_ini_directives;
 *      zend_ini_entry             *error_reporting_ini_entry;
 *      zend_objects_store          objects_store;
 *      zend_object                *exception;
 *      zend_object                *prev_exception;
 *      const zend_op              *opline_before_exception;
 *      zend_op                     exception_op[3];
 *      struct _zend_module_entry  *current_module;
 *      zend_bool                   active;
 *      zend_uchar                  flags;
 *      zend_long                   assertions;
 *      uint32_t                    ht_iterators_count;
 *      uint32_t                    ht_iterators_used;
 *      HashTableIterator          *ht_iterators;
 *      HashTableIterator           ht_iterators_slots[16];
 *      void                       *saved_fpu_cw_ptr;
 *      #if XPFPA_HAVE_CW
 *          XPFPA_CW_DATATYPE       saved_fpu_cw;
 *      #endif
 *      zend_function               trampoline;
 *      zend_op                     call_trampoline_op;
 *      zend_bool                   each_deprecation_thrown;
 *      HashTable                   weakrefs;
 *      zend_bool                   exception_ignore_args;
 *      void                       *reserved[ZEND_MAX_RESERVED_RESOURCES];
 *  };
 * </code>
 */
final class ExecutorGlobals
{
    /**
     * @var Zend
     */
    private Zend $zend;

    /**
     * @var CData
     */
    private CData $ptr;

    /**
     * ExecutorGlobals constructor.
     *
     * @param Zend $zend
     * @param CData $ptr
     */
    public function __construct(Zend $zend, CData $ptr)
    {
        $this->zend = $zend;
        $this->ptr = $ptr;
    }

    /**
     * @return HashTable
     * @noinspection PhpUndefinedFieldInspection
     */
    public function getClasses(): HashTable
    {
        return new HashTable($this->zend, $this->ptr->class_table);
    }

    /**
     * @return HashTable
     * @noinspection PhpUndefinedFieldInspection
     */
    public function getFunctions(): HashTable
    {
        return new HashTable($this->zend, $this->ptr->function_table);
    }

    /**
     * @return HashTable
     * @noinspection PhpUndefinedFieldInspection
     */
    public function getConstants(): HashTable
    {
        return new HashTable($this->zend, $this->ptr->zend_constants);
    }

    /**
     * @return ExecuteData
     * @noinspection PhpUndefinedFieldInspection
     */
    public function getExecuteData(): ExecuteData
    {
        return new ExecuteData($this->zend, $this->ptr->current_execute_data);
    }

    /**
     * @param CData $classEntry
     * @param \Closure $then
     * @return mixed
     */
    public function scoped(CData $classEntry, \Closure $then)
    {
        $pointer = $this->ptr;

        $previous = $pointer->fake_scope;

        $pointer->fake_scope = $classEntry;

        try {
            return $then();
        } finally {
            $pointer->fake_scope = $previous;
        }
    }
}
