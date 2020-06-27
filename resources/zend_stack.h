typedef struct _zend_stack {
    int size, top, max;
    void *elements;
} zend_stack;

#define STACK_BLOCK_SIZE 16
