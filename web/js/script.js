// function for postion arrows if the image is img tag (for the slider)

$(document).ready(function(){
    // $(".multiple-items").on('init, setPosition', function(){
    //     $(".multiple-items .slick-arrow").css("top", Math.round($(".multiple-items .slick-slide img").height()/2));
    // }); //function kato parametyr - callback!!!

    $(".multiple-items").on('init, setPosition', function(){
        $(".multiple-items .image h2").css("top", Math.round($(".multiple-items .image").height()/2+60));
        $(".multiple-items .image h2").css("right", 0 + "%");
        $(".multiple-items .image a span").css("bottom", Math.round($(".multiple-items .image").height()/2-140));
        $(".multiple-items .image a span").css("left", 10 + "%");
    }); //function kato parametyr - callback!!!

    // $('.slick-arrow').slick({
    // preletrow:"<img class='a-left control-c prev slick-prev' src='../images/left_arrow.png'>",
    // nextArrow:"<img class='a-right control-c next slick-next' src='../images/right_arrow.png'>"
    // });

//button go to top
    $('#go-to-top').each(function(){
        $(this).click(function(){
            $('html,body').animate({ scrollTop: 0 }, 'slow');
            return false;
        });
    });
    $('#go-to-top_food').each(function(){
        $(this).click(function(){
            $('html,body').animate({ scrollTop: 0 }, 'slow');
            return false;
        });
    });


//change user icon for signIn/signUp
    $(window).resize(function() {
        let p = $('.collapsed.navbar-toggle');
        let positon = p.position();

        if ($(window).width() < 500){
            // $("#changeimg").attr("src", "images/logo8.png");
            $('.register > .fa').css("font-size", 25 + "px");
            $('.register').css("margin-left", -positon.left*28);
        }
        if ($(window).width() <= 320) {
            // $("#changeimg").attr("src", "images/logo9.png");
            $('.register > .fa').css("font-size", 25 + "px")
                                    .css("margin-right", -positon.left)
                                        .css('transform', 'translate(1300%, -200%)');
        }
        if ($(window).width() < 768){
            $('form.inputbox').empty();

            let div = $('<div class="col-xs-1 col-md-3 text-right">');
            let input = $('<input type="text" name="search" class="inputbox2">');
            div.append(input);

            $('form.inputbox').append(div);
            $('form.inputbox').removeAttr('class');
        }

    });

//adding class nopadding to images in publication and adding/remove classes
//for mobile version

    let div = $('article > div.col-md-6:nth-child(3)');
    let svgImage = $('.reactangle.gradient > div > svg');
    let littleImg = $('.littleImg > article');
    let removeHrLine = $('#contactForm .line-article');
    let removeHrLineHome = $('.lead .line-article');
    let removeRowCustom = $('.row.row_custom');

    if ($(window).width() <= 992){
        div.addClass("nopadding");
        svgImage.toggleClass('images1 images3')
        littleImg.addClass('article2');
        removeHrLine.css('margin-top', '0');
        removeHrLineHome.css('margin-top', '0');
        removeRowCustom.css('margin', '-5%');
    }

//add hide and shown for share icons
    $(".share_icon").on("click", function() {
        let _obj = $(this).parents('.article__content').find('.article__share');
            _obj.toggleClass('hide shown');

        // if (_obj.hasClass("hide")) {
        //     _obj.removeClass("hide")
        //         .addClass('shown');
        // }
        // else {
        //     _obj.addClass("hide").removeClass('shown');
        // }
    });


    $( ".share-btn" ).click(function(e) {
        $('.networks-5').not($(this).next( ".networks-5" )).each(function(){
            $(this).removeClass("active");
        });

        $(this).next( ".networks-5" ).toggleClass( "active" );
    });

//add active class for admin fields
    $( ".btn_admin h3" ).click(function(e) {
        $(this).toggleClass( "active" );
    });


//slick slider option for home page
    $('.multiple-items').slick({
        slidesToShow: 3,
        autoplay: true,
        autoplaySpeed: 2000,
        // appendArrows: $('.arrows'),
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    infinite: true,
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $(".log-in").click(function(){
        $(".signIn").addClass("active-dx");
        $(".signUp").addClass("inactive-sx");
        $(".signUp").removeClass("active-sx");
        $(".signIn").removeClass("inactive-dx");
    });

    $(".back").click(function(){
        $(".signUp").addClass("active-sx");
        $(".signIn").addClass("inactive-dx");
        $(".signIn").removeClass("active-dx");
        $(".signUp").removeClass("inactive-sx");
    });

    let $unique = $('input.unique');
    // let $close = $('button.close');

    $unique.click(function() {

        $checked = $(this).is(':checked') ; // check if that was clicked.
        $unique.removeAttr('checked'); //clear all checkboxes
        $(this).attr('checked', $checked); // update that was clicked.
        $(".close").on('click', function(){
            $unique.removeAttr('checked');
        });
    });

    $(".side-label#forgot-pass").click(function(){
        $('#pass-modal').modal('show');
    });



    //switch search-box in mobile version

    //add hide and shown for delete modal form
    $(".delete-post").on("click", function() {
        let _obj = $(this).next();

        _obj.toggleClass('fade fade.in show');
    });

    $(".btn-success").on("click", function() {
        let _obj = $(".delete-post").next();

        if (_obj.hasClass("fade.in show")) {
            _obj.removeClass("fade.in show")
                .addClass('fade');
        }
        else {
            _obj.addClass("fade.in show").removeClass('fade');
        }
    });

    $(".btn-secondary").on("click", function() {
        let _obj = $(".delete-post").next();

        if (_obj.hasClass("fade.in show")) {
            _obj.removeClass("fade.in show")
                .addClass('fade');
        }
        else {
            _obj.addClass("fade.in show").removeClass('fade');
        }
    });

    $(".close").on("click", function() {
        let _obj = $(".delete-post").next();

        if (_obj.hasClass("fade.in show")) {
            _obj.removeClass("fade.in show")
                .addClass('fade');
        }
        else {
            _obj.addClass("fade.in show").removeClass('fade');
        }
    });

    //selecting last sentence between ""

    let words = $("p.publication_text").text().split(" ");
    let elementHeight = $("p.publication_text").height();
    let lastLine = ' "taken from: ';

    $("p.publication_text").text("");
    $.each(words, function(index, value){
        $("p.publication_text").append(value + " ");
        if ($("p.publication_text").height() == elementHeight)
            lastLine += value + " ";
    });

    let html = $("p.publication_text").html().replace(lastLine.trim(), "</br></br><span class='real_author'>" + lastLine + "</span><br>");
    $("p.publication_text").html(html);

});
