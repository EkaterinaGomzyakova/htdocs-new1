$(document).ready(function(){
    $('.questions-ask').on('click', function(e) {
        $(e.target).closest('.questions-question').find('.question-answer').slideDown();
    });
});