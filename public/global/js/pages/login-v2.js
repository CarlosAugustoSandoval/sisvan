function copyrightPos(){
    var windowHeight = $(window).height();
    if(windowHeight < 700) {
        $('.account-copyright').css('position', 'relative').css('margin-top', 40);
    }
    else {
        $('.account-copyright').css('position', '').css('margin-top', '');
    }
}

$(window).resize(function() {
    copyrightPos();
});

$(function() {
    copyrightPos();
    if($('body').data('page') == 'login'){
        $.backstretch(["../global/images/gallery/login4.jpg", "../global/images/gallery/login3.jpg", "../global/images/gallery/login2.jpg", "../global/images/gallery/login.jpg"], {
            fade: 600,
            duration: 4000
        });
    }
});