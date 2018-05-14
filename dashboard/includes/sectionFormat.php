<?php
/**
 * This file is responsible for the display of sections
 */
//TODO: Add implementation
class sectionFormat
{
    public $images = [
        "../images/placeholder.png",
    ];
    /**
     * <h1>slideFormat</h1>
     * <p>Making the web slide format</p>
     * @param String $bk_source The source of the background image
     * @param String $title The title of the image
     * @param String $details description of the slide
     */
    public function showSlider($bk_source, $title, $details)
    {
        if (!isset($bk_source)) {
           $bk_source = $this->images[0];
        }
        echo '<li data-transition="fade">
                    <!-- MAIN IMAGE -->
                    <img src="' . $bk_source . '"
                         alt="Image"
                         title="slider_bg1-1"
                         data-bgposition="center center"
                         data-bgfit="cover"
                         data-bgrepeat="no-repeat"
                         data-bgparallax="10"
                         class="rev-slidebg"
                         data-no-retina="">';
        //-- LAYER NR. 1 --
        echo ' <div class="tp-caption tp-resizeme"
                         data-x="center"
                         data-hoffset=""
                         data-y="middle"
                         data-voffset="[\'-30\',\'-30\',\'-30\',\'-30\']"
                         data-responsive_offset="on"
                         data-fontsize="[\'60\',\'50\',\'40\',\'30\']"
                         data-lineheight="[\'60\',\'50\',\'40\',\'30\']"
                         data-whitespace="nowrap"
                         data-frames=\'[{"delay":1000,"speed":2000,"frame":"0","from":"y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;","mask":"x:0px;y:[100%];s:inherit;e:inherit;","to":"o:1;","ease":"Power2.easeInOut"},{"delay":"wait","speed":500,"frame":"999","to":"auto:auto;","ease":"Power3.easeInOut"}]\'
                         style="z-index: 5; color: #fff; font-weight: 900;">' . $title . '</div>';
        //-- LAYER NR. 2 --

        echo '<div class="tp-caption tp-resizeme"
                         data-x="center"
                         data-hoffset=""
                         data-y="middle"
                         data-voffset="[\'45\',\'45\',\'45\',\'45\']"
                         data-fontsize="[\'16\', \'16\', \'14\', \'12\']"
                         data-lineheight="[\'16\', \'16\',\'14\', \'12\']"
                         data-whitespace="nowrap"
                         data-transform_idle="o:1;"
                         data-transform_in="opacity:0;s:300;e:Power2.easeInOut;"
                         data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;"
                         data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;"
                         data-start="3000"
                         data-splitin="chars"
                         data-splitout="none"
                         data-basealign="slide"
                         data-responsive="off"
                         data-elementdelay="0.05"
                         style="z-index: 9; font-weight: 400; color: rgba(255, 255, 255, 0.8); font-family: Raleway;">' . $details . '</div>
                </li>';
    }

    /**
     * <h1>showFeature</h1>
     * <p>Show feature section</p>
     * @param $featureList The list of features
     */
    public function showFeature($featureList)
    {
        echo '<div class = "col-md-7">'
            . '<div data-slider-id = "features" id = "features_slider" class = "owl-carousel">';
        //image display
        for ($count = 0; $count < count($featureList); $count++) {
            if (isset($featureList[$count][3])) {
                $this->showImage($featureList[$count][3]);
            } else {
                $image = $this->images[0];
                $this->showImage($image);
            }
        }
        echo '</div>
        </div>';
        //description display
        echo '<div class="col-md-5">
                <div class="owl-thumbs" data-slider-id="features">';
        for ($count = 0; $count < count($featureList); $count++) {
            $title = $featureList[$count][1];
            $description = $featureList[$count][2];
            $this->showDescription($title, $description);
        }
        echo '</div>
            </div>';
    }

    private function showImage($image)
    {
        echo '
        <div><img src = "' . $image . '" class = "img-responsive" alt = "Image"></div>
        ';
    }

    private function showDescription($title, $description)
    {
        echo '<div class="owl-thumb-item">
                        <span class="media-left"><i class="flaticon-food"></i></span>
                        <div class="media-body">
                            <h5>' . $title . '</h5>
                            <p>' . $description . '</p>
                        </div>
                    </div>';
    }

}
