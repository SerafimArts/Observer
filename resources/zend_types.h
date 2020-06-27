
#ifdef ZEND_ENABLE_ZVAL_LONG64
typedef int64_t zend_long;
typedef uint64_t zend_ulong;
typedef int64_t zend_off_t;
#endif
#ifndef ZEND_ENABLE_ZVAL_LONG64
typedef int32_t zend_long;
typedef uint32_t zend_ulong;
typedef int32_t zend_off_t;
#endif

#if __php_observer_version__ >= 8.0
typedef bool zend_bool;
#endif
#if __php_observer_version__ < 8.0
typedef unsigned char zend_bool;
#endif

typedef unsigned char zend_uchar;
typedef uintptr_t zend_type;

typedef enum {
  SUCCESS =  0,
  FAILURE = -1,        /* this MUST stay a negative number, or it may affect functions! */
} ZEND_RESULT_CODE;

typedef intptr_t zend_intptr_t;
typedef uintptr_t zend_uintptr_t;

typedef struct _zend_object_handlers zend_object_handlers;
typedef struct _zend_class_entry     zend_class_entry;
typedef union  _zend_function        zend_function;
typedef struct _zend_execute_data    zend_execute_data;

typedef struct _zval_struct     zval;

typedef struct _zend_refcounted zend_refcounted;
typedef struct _zend_string     zend_string;
typedef struct _zend_array      zend_array;
typedef struct _zend_object     zend_object;
typedef struct _zend_resource   zend_resource;
typedef struct _zend_reference  zend_reference;
typedef struct _zend_ast_ref    zend_ast_ref;
typedef struct _zend_ast        zend_ast;

typedef int  (*compare_func_t)(const void *, const void *);
typedef void (*swap_func_t)(void *, void *);
typedef void (*sort_func_t)(void *, size_t, size_t, compare_func_t, swap_func_t);
typedef void (*dtor_func_t)(zval *pDest);
typedef void (*copy_ctor_func_t)(zval *pElement);

typedef union _zend_value {
    zend_long         lval;                /* long value */
    double            dval;                /* double value */
    zend_refcounted  *counted;
    zend_string      *str;
    zend_array       *arr;
    zend_object      *obj;
    zend_resource    *res;
    zend_reference   *ref;
    zend_ast_ref     *ast;
    zval             *zv;
    void             *ptr;
    zend_class_entry *ce;
    zend_function    *func;
    struct {
        uint32_t w1;
        uint32_t w2;
    } ww;
} zend_value;

struct _zval_struct {
    zend_value        value;            /* value */
    union {
        struct {
            zend_uchar    type;            /* active type */
            zend_uchar    type_flags;
            union {
                uint16_t  extra;        /* not further specified */
            } u;
        } v;
        uint32_t type_info;
    } u1;
    union {
        uint32_t     next;                 /* hash collision chain */
        uint32_t     cache_slot;           /* cache slot (for RECV_INIT) */
        uint32_t     opline_num;           /* opline number (for FAST_CALL) */
        uint32_t     lineno;               /* line number (for ast nodes) */
        uint32_t     num_args;             /* arguments number for EX(This) */
        uint32_t     fe_pos;               /* foreach position */
        uint32_t     fe_iter_idx;          /* foreach iterator index */
        uint32_t     access_flags;         /* class constant access flags */
        uint32_t     property_guard;       /* single property guard */
        uint32_t     constant_flags;       /* constant flags */
        uint32_t     extra;                /* not further specified */
    } u2;
};

typedef struct _zend_refcounted_h {
    uint32_t         refcount;            /* reference counter 32-bit */
    union {
        uint32_t type_info;
    } u;
} zend_refcounted_h;

struct _zend_refcounted {
    zend_refcounted_h gc;
};

struct _zend_string {
    zend_refcounted_h gc;
    zend_ulong        h;                /* hash value */
    size_t            len;
    char              val[1];
};

typedef struct _Bucket {
    zval              val;
    zend_ulong        h;                /* hash value (or numeric index)   */
    zend_string      *key;              /* string key or NULL for numerics */
} Bucket;

typedef struct _zend_array HashTable;

struct _zend_array {
    zend_refcounted_h gc;
    union {
        struct {
            zend_uchar    flags;
            zend_uchar    _unused;
            zend_uchar    nIteratorsCount;
            zend_uchar    _unused2;
        } v;
        uint32_t flags;
    } u;
    uint32_t          nTableMask;
    Bucket           *arData;
    uint32_t          nNumUsed;
    uint32_t          nNumOfElements;
    uint32_t          nTableSize;
    uint32_t          nInternalPointer;
    zend_long         nNextFreeElement;
    dtor_func_t       pDestructor;
};

typedef uint32_t HashPosition;

typedef struct _HashTableIterator {
    HashTable    *ht;
    HashPosition  pos;
} HashTableIterator;

struct _zend_object {
    zend_refcounted_h gc;
    uint32_t          handle; // TODO: may be removed ???
    zend_class_entry *ce;
    const zend_object_handlers *handlers;
    HashTable        *properties;
    zval              properties_table[1];
};

struct _zend_resource {
    zend_refcounted_h gc;
    int               handle; // TODO: may be removed ???
    int               type;
    void             *ptr;
};

typedef struct {
    size_t num;
    size_t num_allocated;
    struct _zend_property_info *ptr[1];
} zend_property_info_list;

typedef union {
    struct _zend_property_info *ptr;
    uintptr_t list;
} zend_property_info_source_list;

struct _zend_reference {
    zend_refcounted_h              gc;
    zval                           val;
    zend_property_info_source_list sources;
};

struct _zend_ast_ref {
    zend_refcounted_h gc;
    /*zend_ast        ast; zend_ast follows the zend_ast_ref structure */
};
