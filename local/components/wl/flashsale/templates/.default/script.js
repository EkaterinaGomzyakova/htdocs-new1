let strFlashsale = $('.flashsale_outer').attr('data-flashsale-end');
let strTicker = $('.flashsale_outer').attr('data-flashsale-ticker');
var isFlashsaleSecondsTicker = strTicker === 'seconds';
var dtFlashsaleEnd = Date.parse(strFlashsale);
var intervalFlashsale = setInterval(() => {
    let totalSeconds = parseInt((dtFlashsaleEnd - new Date()) / 1000);
    if (totalSeconds < 0) {
        clearInterval(intervalFlashsale);
        $('.flashsale_container').hide();
        return;
    }
    let totalMinutes = Math.floor(totalSeconds / 60);
    let seconds = totalSeconds % 60;
    let hours = Math.floor(totalMinutes / 60);
    let minutes = totalMinutes % 60;
    let days = Math.floor(hours / 24);
    let timer1 = 0;
    let timer2 = 0;
    let timer3 = 0;
    if (isFlashsaleSecondsTicker) {
        timer1 = hours;
        timer2 = minutes;
        timer3 = seconds;
    } else {
        timer1 = days;
        timer2 = hours % 24;
        timer3 = minutes;
    }
    $('.flashsale_timer1_digits').text(String(timer1).padStart(2, '0'));
    $('.flashsale_timer2_digits').text(String(timer2).padStart(2, '0'));
    $('.flashsale_timer3_digits').text(String(timer3).padStart(2, '0'));
}, 1000);
