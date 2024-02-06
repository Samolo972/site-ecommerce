document.querySelectorAll('.btn-increment').forEach(function(link) {
    link.addEventListener('click', function(event) {
        event.preventDefault();
        var input = this.closest('.input-group').querySelector('.input-number');
        input.value = parseInt(input.value, 10) + 1;
    });
});

document.querySelectorAll('.btn-decrement').forEach(function(link) {
    link.addEventListener('click', function(event) {
        event.preventDefault();
        var input = this.closest('.input-group').querySelector('.input-number');
        var currentValue = parseInt(input.value, 10);
        if (currentValue > 0) {
            input.value = currentValue - 1;
        }
    });
});
