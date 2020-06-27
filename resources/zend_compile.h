
typedef struct _zend_property_info {
    uint32_t offset; /* property offset for object properties or
                          property index for static properties */
    uint32_t flags;
    zend_string *name;
    zend_string *doc_comment;
    zend_class_entry *ce;
    zend_type type;
} zend_property_info;
