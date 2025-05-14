<?php

namespace BuddyBot\Admin\Html\Views;

class MoRoot extends \BuddyBot\Admin\Html\MoRoot
{
    public $config;
    protected $sql;

    protected function setSql()
    {
        $class_name = (new \ReflectionClass($this))->getShortName();
        $this->config = \BuddyBot\MoConfig::getInstance();
        $file_path = $this->config->getRootPath() . 'admin/sql/' . strtolower($class_name) . '.php';

        if (file_exists($file_path)) {
            $class_name = '\BuddyBot\Admin\Sql\\' . $class_name;
            $this->sql = $class_name::getInstance(); 
        }
    }

    protected function alertContainer()
    {
        echo '<div id="buddybot-alert-container" class="notice-error buddybot-ms-0" style="display:none;">';
        echo '<p></p>';
        echo '</div>';
    }

    protected function moSpinner()
    {
        echo '<div class="buddybot-dataload-spinner spinner-border spinner-border-sm text-dark" role="status">';
        echo '<span class="visually-hidden">Loading...</span>';
        echo '</div>';
    }

    protected function toastContainer()
    {
        echo '<div id="buddybot-toast-container">';
        echo '<div class="buddybot-toast">';
        echo '<div class="buddybot-toast-content">';
        echo '<span class="toast-message"></span>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    protected function documentationContainer($link = '')
    {
        echo '<div class="buddybot-docs-container buddybot-mb-3">';
            echo '<div class="buddybot-docs-inner  buddybot-d-flex buddybot-align-items-center buddybot-align-item-center buddybot-p-2">';
    
                echo '<div class="buddybot-docs-content">';
                    echo '<div class="buddybot-banner-head buddybot-text-dark">';
                        esc_html_e('How is going?', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</div>';
                    echo '<div class="buddybot-banner-text">';
                        esc_html_e(' Welcome to BuddyBot! If you\'re just getting started or have questions, these resources can help.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</div>';
                    echo '<div class="buddybot-docs-actions">';
                        echo '<a href="' . esc_url($link) . '" type="button" class="button button-primary" target="_blank">';
                            esc_html_e('View Documentation', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                        echo '</a>';
                        echo '<a href="https://getbuddybot.com/starter-guide/" type="button" class="button button-primary" target="_blank">';
                            esc_html_e('Starter Guide', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                        echo '</a>';
                        echo '<a href="https://wordpress.org/support/plugin/buddybot-ai-custom-ai-assistant-and-chat-agent/" type="button" class="button button-secondary" target="_blank">';
                            esc_html_e('Get Support', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                        echo '</a>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }

    protected function buddybotFooterBanner()
    {
        echo '<div class="buddybot-footer-banner">';
            echo '<div class="buddybot-docs-inner  buddybot-justify-content-between buddybot-align-items-center buddybot-p-2">';

                echo '<div class="buddybot-banner-icon">';
                    echo '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:serif="http://www.serif.com/" width="100%" height="100%" viewBox="0 0 854 800" version="1.1" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:1.5;">
    <g transform="matrix(1,0,0,1,-602.144,341.281)">
        <path d="M824.097,370.84C824.097,370.84 1008.94,443.042 1227.16,371.654C1227.16,371.654 1258.31,383.628 1267.52,391.877C1267.52,391.877 1066.58,513.866 788.406,391.877" style="fill:rgb(218,218,217);"/>
    </g>
    <g transform="matrix(1,0,0,1.12358,-85.7,-104.575)">
        <ellipse cx="513.849" cy="138.659" rx="38.815" ry="34.671" style="fill:rgb(25,137,230);"/>
    </g>
    <g transform="matrix(1,0,0,1,-85.7,-83.1544)">
        <path d="M861.42,446.897L895.988,461.018L918.67,493.778L927.572,533.966L927.572,599.009L918.67,619.197L876.812,650.088L861.42,643.371L861.42,446.897Z" style="fill:rgb(25,137,230);stroke:black;stroke-width:1px;"/>
    </g>
    <g transform="matrix(-1.00718,0,0,1,941.095,-90.3056)">
        <path d="M861.42,446.897L895.988,461.018L918.67,493.778L927.572,533.966L927.572,599.009L918.67,619.197L876.812,650.088L861.42,643.371L861.42,446.897Z" style="fill:rgb(25,137,230);"/>
    </g>
    <g transform="matrix(0.1,0,0,-0.1,-85.7,940.846)">
        <path d="M4993,9396C4866,9365 4724,9253 4668,9140C4548,8897 4618,8604 4830,8460C4864,8437 4904,8414 4920,8408L4950,8398L4950,7623L4758,7617C3546,7576 2580,7224 2044,6630C1865,6430 1701,6155 1644,5955L1626,5893L1561,5881C1438,5858 1301,5785 1187,5680C1068,5571 985,5437 926,5260C872,5096 857,4986 857,4760C857,4590 861,4549 884,4438C913,4294 945,4199 1002,4085C1125,3839 1332,3675 1566,3637L1639,3626L1666,3555C1759,3314 1926,3064 2139,2847C2316,2666 2499,2529 2723,2410L2821,2357L2758,2323C2526,2194 2271,1974 2143,1793C2077,1699 2019,1579 2013,1525C2008,1489 2012,1478 2038,1449C2063,1421 2075,1415 2108,1415C2160,1415 2191,1444 2230,1525C2327,1728 2520,1934 2741,2071C2786,2099 2883,2151 2955,2187L3086,2251L3231,2207C3866,2010 4489,1930 5260,1943C5958,1956 6567,2049 7047,2216L7149,2252L7247,2210C7369,2158 7556,2054 7646,1986C7807,1866 7974,1672 8040,1526C8077,1445 8109,1415 8162,1415C8195,1415 8207,1421 8232,1449C8271,1493 8267,1528 8210,1641C8074,1912 7800,2173 7491,2329C7418,2366 7414,2359 7545,2430C8015,2683 8377,3073 8571,3535L8609,3625L8667,3636C8942,3687 9172,3894 9291,4198C9357,4368 9380,4487 9387,4691C9401,5111 9295,5433 9070,5658C8962,5766 8840,5837 8697,5875L8619,5895L8580,5998C8394,6491 8009,6896 7472,7164C6933,7433 6342,7570 5538,7612L5300,7624L5300,8397L5344,8414C5457,8457 5582,8591 5625,8715C5648,8779 5656,8929 5640,9003C5603,9184 5432,9357 5250,9399C5190,9413 5057,9411 4993,9396ZM5264,9192C5481,9090 5532,8811 5364,8643C5291,8571 5250,8555 5135,8555C5046,8555 5029,8558 4985,8582C4920,8616 4855,8686 4822,8758C4802,8803 4796,8830 4796,8891C4796,8958 4801,8977 4828,9033C4914,9206 5094,9272 5264,9192ZM5796,7386C6455,7323 7039,7162 7465,6927C8092,6581 8419,6073 8546,5250C8567,5113 8576,4671 8561,4488C8521,3980 8401,3585 8190,3272C7890,2826 7411,2515 6765,2345C6264,2214 5769,2157 5135,2158C4511,2158 4045,2209 3560,2330C2741,2534 2222,2906 1933,3495C1631,4113 1586,5105 1827,5819C1931,6127 2056,6335 2269,6554C2745,7042 3531,7326 4615,7400C4817,7413 5606,7404 5796,7386ZM1544,5613C1536,5586 1518,5511 1505,5444C1414,4997 1423,4438 1529,3996C1546,3926 1560,3866 1560,3864C1560,3842 1459,3887 1390,3939C1246,4047 1142,4239 1093,4485C1067,4619 1069,4929 1098,5060C1140,5254 1204,5382 1318,5502C1376,5563 1411,5589 1473,5620C1516,5642 1554,5660 1555,5660C1557,5660 1552,5639 1544,5613ZM8795,5602C8871,5557 8974,5452 9023,5369C9113,5216 9158,5047 9167,4825C9178,4544 9133,4317 9032,4150C8956,4026 8861,3933 8759,3886C8690,3853 8677,3853 8690,3883C8703,3914 8736,4050 8754,4150C8829,4568 8817,5127 8725,5515C8712,5570 8698,5623 8694,5634C8688,5650 8690,5651 8711,5645C8724,5641 8762,5622 8795,5602Z" style="fill:rgb(45,69,96);fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(1,0,0,1,-85.7,-83.1544)">
        <rect x="406.282" y="604.648" width="235.642" height="86.794" style="fill:rgb(25,137,230);"/>
    </g>
    <g transform="matrix(0.1,0,0,-0.1,-85.7,940.846)">
        <path d="M4790,6604C3976,6574 3348,6470 2975,6302C2761,6206 2590,6063 2465,5876C2358,5715 2279,5489 2238,5227C2208,5037 2206,4591 2233,4410C2326,3803 2571,3410 2989,3200C3205,3092 3598,2987 4000,2931C5199,2763 6552,2866 7219,3176C7788,3441 8072,4032 8047,4895C8027,5566 7822,5987 7391,6239C7097,6412 6537,6535 5820,6584C5610,6599 4979,6611 4790,6604ZM3863,5565C3946,5543 3996,5515 4056,5455C4112,5399 4167,5301 4197,5204C4222,5122 4222,4883 4197,4790C4110,4463 3850,4303 3606,4427C3492,4485 3391,4636 3355,4804C3331,4913 3338,5116 3370,5215C3430,5404 3546,5530 3694,5566C3763,5583 3796,5583 3863,5565ZM6539,5570C6621,5555 6696,5512 6758,5445C6908,5284 6958,5016 6884,4762C6849,4643 6817,4587 6735,4504C6647,4415 6584,4388 6467,4388C6397,4387 6381,4391 6315,4424C6224,4469 6164,4533 6110,4645C6055,4759 6036,4862 6043,5019C6047.57,5134.39 6070.84,5235.55 6110,5319.04C6182.16,5472.86 6308.3,5566.68 6471,5579C6480,5579 6510,5576 6539,5570ZM4470,4045C4485,4037 4515,4004 4534,3971C4614,3839 4759,3745 4937,3708C5017,3692 5059,3689 5170,3693C5437,3703 5597,3782 5721,3966C5748,4007 5779,4044 5789,4050C5822,4067 5860,4061 5887,4034C5910,4011 5912,4003 5907,3954C5888,3766 5645,3571 5346,3503C5254,3482 5012,3480 4915,3499C4741,3533 4603,3605 4489,3719C4398,3812 4358,3885 4356,3966C4355,4013 4359,4025 4379,4041C4407,4064 4431,4065 4470,4045Z" style="fill:rgb(45,69,96);fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(0.1,0,0,-0.1,-85.7,940.846)">
        <path d="M3737,5553C3755,5551 3787,5551 3807,5553C3828,5555 3814,5557 3775,5557C3737,5557 3720,5555 3737,5553Z" style="fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(0.1,0,0,-0.1,-85.7,940.846)">
        <path d="M3678,5543C3685,5540 3694,5541 3697,5544C3701,5547 3695,5550 3684,5549C3673,5549 3670,5546 3678,5543Z" style="fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(0.1,0,0,-0.1,-85.7,940.846)">
        <path d="M3760,5531C3760,5527 3748,5523 3733,5522C3717,5522 3702,5521 3698,5520C3693,5519 3687,5517 3683,5516C3669,5513 3592,5461 3585,5450C3582,5444 3574,5443 3568,5446C3561,5450 3560,5448 3564,5442C3568,5435 3566,5430 3561,5430C3551,5430 3522,5391 3478,5320C3469,5305 3460,5282 3458,5269C3456,5252 3450,5246 3436,5248C3419,5251 3418,5250 3429,5236C3438,5225 3439,5220 3431,5220C3425,5220 3420,5207 3420,5192C3420,5177 3414,5158 3408,5151C3397,5140 3398,5139 3408,5145C3420,5151 3420,5148 3409,5129C3402,5116 3394,5098 3393,5090C3385,5057 3384,4923 3391,4890C3396,4871 3401,4842 3404,4825C3407,4809 3414,4780 3419,4763C3424,4745 3426,4730 3423,4730C3420,4730 3425,4719 3434,4706C3446,4688 3446,4680 3437,4677C3430,4674 3433,4671 3443,4671C3453,4670 3459,4666 3456,4661C3450,4651 3494,4608 3503,4615C3507,4618 3506,4615 3501,4609C3491,4596 3495,4590 3551,4531C3571,4510 3609,4485 3636,4475C3663,4465 3694,4450 3705,4443C3729,4427 3798,4426 3837,4441C3852,4447 3876,4454 3890,4457C3904,4459 3917,4465 3918,4471C3920,4476 3926,4478 3931,4474C3936,4471 3940,4474 3940,4479C3940,4485 3945,4490 3952,4490C3985,4490 4132,4699 4123,4732C4121,4736 4124,4740 4129,4740C4163,4740 4174,5197 4140,5208C4135,5210 4133,5216 4136,5221C4139,5227 4131,5245 4117,5263C4104,5281 4089,5310 4085,5328C4071,5394 3923,5513 3840,5525C3832,5527 3810,5530 3793,5534C3775,5537 3760,5536 3760,5531Z" style="fill:rgb(25,137,230);fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(0.1,0,0,-0.1,-85.7,940.846)">
        <path d="M3353,4975C3353,4931 3355,4914 3357,4938C3359,4961 3359,4997 3357,5018C3355,5038 3353,5019 3353,4975Z" style="fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(0.1,0,0,-0.1,-85.7,940.846)">
        <path d="M3371,4794C3371,4783 3374,4780 3377,4788C3380,4795 3379,4804 3376,4807C3373,4811 3370,4805 3371,4794Z" style="fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(0.1,0,0,-0.1,-85.7,940.846)">
        <path d="M3848,4413C3854,4411 3866,4411 3873,4413C3879,4416 3874,4418 3860,4418C3846,4418 3841,4416 3848,4413Z" style="fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(0.1,0,0,-0.1,-85.7,940.846)">
        <path d="M6433,5553C6442,5551 6456,5551 6463,5553C6469,5556 6462,5558 6445,5558C6429,5558 6423,5556 6433,5553Z" style="fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(0.1,0,0,-0.1,-85.7,940.846)">
        <path d="M6493,5553C6502,5551 6516,5551 6523,5553C6529,5556 6522,5558 6505,5558C6489,5558 6483,5556 6493,5553Z" style="fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(0.1,0,0,-0.1,-85.7,940.846)">
        <path d="M6354,5498C6338,5486 6323,5473 6320,5469C6317,5466 6305,5460 6294,5456C6226,5435 6111,5218 6095,5080C6093,5061 6091,5043 6090,5040C6086,5026 6090,4888 6095,4881C6098,4876 6102,4846 6105,4815C6107,4784 6114,4753 6119,4746C6125,4739 6127,4730 6123,4727C6120,4723 6125,4720 6135,4720C6145,4720 6150,4717 6147,4714C6144,4710 6145,4699 6151,4689C6158,4676 6157,4670 6150,4670C6139,4670 6147,4658 6185,4618C6196,4607 6198,4601 6190,4605C6177,4613 6177,4612 6189,4594C6212,4564 6250,4528 6250,4537C6250,4541 6255,4537 6261,4528C6271,4510 6370,4456 6370,4467C6370,4471 6377,4465 6385,4454C6393,4444 6400,4441 6400,4447C6400,4456 6407,4457 6423,4451C6463,4436 6547,4444 6605,4470C6622,4477 6639,4482 6643,4482C6647,4481 6653,4487 6656,4495C6660,4503 6668,4510 6676,4510C6684,4510 6690,4515 6690,4520C6690,4526 6701,4535 6714,4541C6731,4549 6736,4558 6732,4573C6729,4587 6731,4591 6738,4586C6745,4582 6750,4585 6750,4593C6750,4601 6757,4610 6765,4614C6773,4617 6780,4627 6780,4637C6780,4647 6789,4666 6800,4680C6811,4694 6820,4712 6820,4721C6820,4729 6825,4741 6832,4748C6839,4755 6840,4760 6836,4760C6831,4760 6833,4767 6840,4775C6847,4784 6850,4793 6847,4797C6843,4800 6845,4811 6850,4821C6856,4831 6863,4863 6865,4892C6868,4921 6873,4953 6876,4964C6880,4974 6878,4980 6874,4977C6869,4974 6865,5002 6865,5038C6865,5075 6861,5114 6856,5125C6850,5136 6847,5153 6849,5164C6850,5174 6847,5179 6842,5176C6836,5172 6834,5178 6837,5190C6840,5202 6836,5212 6827,5216C6818,5219 6813,5230 6815,5242C6818,5253 6814,5272 6808,5284C6801,5295 6796,5308 6797,5311C6798,5314 6784,5334 6765,5355C6746,5376 6730,5396 6730,5401C6730,5406 6725,5410 6719,5410C6714,5410 6711,5414 6714,5419C6717,5423 6704,5436 6685,5446C6666,5456 6650,5470 6650,5478C6650,5485 6637,5493 6620,5497C6604,5500 6590,5506 6590,5510C6590,5514 6544,5518 6487,5519C6394,5521 6381,5519 6354,5498Z" style="fill:rgb(25,137,230);fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(1.26759,0,0,1.20929,-132.241,-196.723)">
        <ellipse cx="547.336" cy="528.746" rx="35.324" ry="50.164" style="fill:rgb(25,137,230);"/>
    </g>
    <g transform="matrix(1.26759,0,0,1.20929,-401.886,-197)">
        <ellipse cx="547.336" cy="528.746" rx="35.324" ry="50.164" style="fill:rgb(25,137,230);"/>
    </g>
</svg>';
echo '</div>';
              
                echo '<div class="buddybot-banner-text">';
                    echo '<div class="buddybot-banner-head buddybot-fw-bold buddybot-fs-6 buddybot-text-dark buddybot-mb-2">';
                        esc_html_e('Get Expert Help with AI Integration for Your Site', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</div>';
                        echo '<div class="buddybot-text-dark buddybot-mb-4">';
                                esc_html_e('Struggling with AI? Our team is here to help you unlock the full potential of BuddyBot.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                        echo '</div>';
                    echo '<ul class="buddybot-text-dark buddybot-mb-0">';

                    echo '<div class="buddybot-text-dark buddybot-fw-bold buddybot-mb-2">';
                        esc_html_e('What We Offer:', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</div>';

                      echo '<li class="buddybot-d-flex buddybot-align-item-center buddybot-gap-2">';
                          echo '<span>';
                            echo '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000" style="flex-shrink: 0;"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>';
                           echo '</span>';
                         echo '<span>' . esc_html__('AI customization that fits your needs.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
                       echo '</li>';

                        echo '<li class="buddybot-d-flex buddybot-align-item-center buddybot-gap-2">';
                              echo '<span>';
                              echo '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>';
                                  echo '</span>';
                               echo '<span>' . esc_html__('Step-by-step guidance on training BuddyBot with your content.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
                               
                            echo '</li>';

                        echo '<li class="buddybot-d-flex buddybot-align-item-center buddybot-gap-2 buddybot-mb-0">';
                           echo '<span>';
                           echo '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>';
                             echo '</span>';
                            echo '<span class="buddybot-fw-bold">' . esc_html__('BuddyBot extensions are FREE for early adopters!', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
                        echo '</li>';

                    echo '</ul>';
                echo '</div>';
                echo '<div>';
                    echo '<a href="https://getbuddybot.com/starter-guide/" type="button" class="button banner-button-primary" target="_blank">';
                        esc_html_e('Contact Us Now!', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</a>';
            echo '</div>';
        echo '</div>';
    }

    protected function pageModals()
    {
        
    }

    public function pageJs()
    {
        $name = str_replace('buddybot-','', sanitize_text_field($_GET['page']));
        $js_file_url = $this->config->getRootUrl() . 'admin/js/' . $name . '.js';
        wp_enqueue_script(sanitize_text_field($_GET['page']), sanitize_url($js_file_url), array('jquery'), BUDDYBOT_PLUGIN_VERSION);

        if (method_exists($this, 'getInlineJs')) {
            wp_add_inline_script(sanitize_text_field($_GET['page']), $this->getInlineJs());
        }

        $requests_class = '\BuddyBot\Admin\Requests\\' . $name;
        $requests = new $requests_class();
        wp_add_inline_script(sanitize_text_field($_GET['page']), $requests->requestsJs());
    }
}