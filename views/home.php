<?php require '../dashboard/includes/interface.php';?>
<?php
$title = "Home";
ob_start();
?>
<!-- ========== REVOLUTION SLIDER ========== -->
<section id="slider" class="full_slider">
    <div class="rev_slider_wrapper fullscreen-container">
        <div id="fullscreen_slider" class="rev_slider fullscreenbanner gradient_slider" style="display:none">
            <ul>
                <?php $web->showContent("slider", 1, ["image", "title", "description"]);?>
            </ul>
        </div>
    </div>
</section>
<!-- ========== FEATURES ========== -->
<section class="lightgrey_bg" id="features">
    <div class="container">
        <div class="main_title mt_wave a_center">
            <h2>OUR AWESOME SERVICES</h2>
        </div>
        <p class="main_description a_center">Health care brought to your home.</p>
        <div class="row">
          <?php $web->showContent("feature", 2, ["title", "description", "image"]);?>
        </div>
    </div>
</section>
<!-- ========== SPECIALISTS ========== -->
<section id="testimonials" class="white_bg" style="background:url('../images/section_holder.jpg')">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="main_title mt_wave a_left">
                    <h2>OUR SPECIALIST</h2>
                </div>
                <p class="main_description" style="color:#fff">Mature and very kind person, has a passion to elderly people, and  people with disabilities. I want to help those who are staying with them in their houses and leaving them with care takers with no experience.
                    <br><br> I am available 24 hrs 7/7. I drive to your house or home for the aged and visit. I take them out to change the environment. For any other questions please <a href="#contact">contact us</a>.</p>
            </div>

            <div class="col-md-6">
                <div id="testimonials_slider" class="owl-carousel">
                    <!-- ITEM -->
                    <div class="item">
                        <img src="../images/user_placeholder.jpg" alt="Image">
                        <div class="review_content">
                            <p>Nurse /Midwife /Trauma counselor with long experience in different countries.</p>
                            <div class="review_rating">
                                <i class="fa fa-star" aria-hidden="true"></i>
                                <i class="fa fa-star" aria-hidden="true"></i>
                                <i class="fa fa-star" aria-hidden="true"></i>
                                <i class="fa fa-star" aria-hidden="true"></i>
                                <i class="fa fa-star" aria-hidden="true"></i>
                            </div>
                            <div class="review_author">Sr Lilian Naomi, Rwanda</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========== CONTACT ========== -->
<section class="white_bg" id="contact">
    <div class="container">
        <div class="main_title mt_wave mt_yellow a_center">
            <h2>CONTACT US</h2>
        </div>
        <p class="main_description a_center">Contact us for any support you may need.</p>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="contact-items">
                        <div class="col-md-4 col-sm-4">
                            <div class="contact-item">
                                <i class="glyphicon glyphicon-map-marker"></i>
                                <h6>Kigali - Rwanda</h6>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="contact-item">
                                <i class="glyphicon glyphicon-phone-alt"></i>
                                <h6>+250 788308008</h6>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="contact-item">
                                <i class="fa fa-envelope"></i>
                                <h6>info@izerehomenursing.com</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <form method="post" id="contact-form">
                    <div id="contact-result"></div>
                    <div class="form-group">
                        <input class="form-control" required name="name" placeholder="Your Name" type="text">
                    </div>
                    <div class="form-group">
                        <input class="form-control" required name="email" type="email" placeholder="Your Email Address">
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" required name="message" placeholder="Your Message"></textarea>
                    </div>
                    <button class="button btn_lg btn_blue btn_full upper" type="submit">Send message</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php $content = ob_get_clean();?>
<?php include '../layout/layout_main.php';?>