document.addEventListener("DOMContentLoaded", function () {
const { registerBlockType } = wp.blocks;
const { createElement, useState, useEffect, useRef } = wp.element;
const { __ } = wp.i18n;
const { InspectorControls, useBlockProps, BlockControls, AlignmentToolbar } = wp.blockEditor;
const { TextControl, SelectControl, PanelBody, Spinner, ButtonGroup, Button, Dashicon, Notice } = wp.components;
const { dispatch } = wp.data;
const el = wp.element.createElement;

const BuddyBotIcon = el('svg', { width: 20, height: 20, viewBox: "0 0 1024 1024" },
    el('path', {
      fill: '#000000',
      opacity: '1.000000',
      stroke: 'none',
      d: 'M552.066284,812.318970 C539.770569,812.990234 527.960693,813.426880 516.129700,813.251587 C489.833221,812.862122 463.531403,812.298279 437.315399,810.219360 C398.963318,807.178162 360.977417,801.834351 324.757751,788.008301 C265.295441,765.309570 228.903610,722.058899 212.849655,661.083313 C211.401245,655.582031 210.096268,652.374756 203.290466,651.756775 C177.681717,649.431580 161.530365,630.137878 157.295029,609.633423 C156.458221,605.582275 155.824020,601.405579 155.803177,597.283203 C155.664886,569.952942 155.534378,542.620117 155.764740,515.291199 C155.980286,489.718262 173.217575,467.441437 197.069168,461.445587 C200.749969,460.520325 204.607162,459.841492 208.379028,459.859009 C212.324860,459.877411 213.938858,458.297089 214.903259,454.654327 C220.720245,432.682770 230.109756,412.381927 243.915985,394.238525 C270.463348,359.351471 306.157013,338.766022 347.977509,327.759186 C370.711670,321.775757 393.869751,318.577423 417.260132,316.590454 C437.994354,314.829163 458.729431,313.603210 479.546692,313.611328 C490.475067,313.615570 490.898010,312.591644 491.799927,301.805603 C492.961975,287.909180 494.751740,274.065216 496.275940,260.199188 C497.074280,252.936356 497.778839,245.661026 498.725800,238.417511 C499.297150,234.047073 497.644653,231.475662 493.866669,229.124863 C475.835693,217.905273 468.210297,195.911041 475.065308,175.932297 C481.139191,158.230042 496.277985,147.516815 514.953247,147.704895 C531.520874,147.871735 546.816711,159.764877 552.288513,176.734528 C558.997437,197.540878 551.257568,218.440063 532.476379,229.762329 C529.837952,231.352905 527.914062,232.803513 528.299255,236.279358 C529.979919,251.448654 531.576416,266.627625 533.123596,281.811218 C533.948853,289.910126 534.738770,298.016907 535.295776,306.137177 C535.590515,310.434570 536.947876,312.480438 541.812805,312.903381 C556.756775,314.202423 571.749084,313.546265 586.692688,314.650696 C624.844910,317.470398 663.083923,319.924225 699.290894,333.809814 C750.711548,353.529846 788.526855,387.369720 807.726074,440.056580 C809.602600,445.206207 811.091492,450.506653 812.550842,455.794891 C813.333496,458.631012 814.719604,459.778168 817.808533,459.756622 C848.651611,459.541595 871.976746,484.657318 872.258118,515.702148 C872.502869,542.697754 872.435974,569.697815 872.284912,596.694702 C872.096985,630.289429 846.982422,650.471558 824.564392,651.423645 C819.078735,651.656616 816.264709,654.001709 814.952881,659.825500 C810.201782,680.918396 802.368347,700.849304 790.538757,719.091736 C770.670776,749.730225 742.975647,770.833191 709.909180,785.403931 C682.549133,797.460144 653.650818,803.616394 624.170349,807.277466 C600.401062,810.229248 576.523071,811.999084 552.066284,812.318970 z'
    })
);
  

registerBlockType('buddybot/chat', {
    title: __('BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
    description: __('Embed a BuddyBot into your page or post.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
    icon: BuddyBotIcon,
    category: 'widgets',
    supports: {
        align: true,
        className: true,
        html: false,
    },
    attributes: {
        selectedBuddyBot: { type: 'string' },
        customClass: { type: 'string' },
        align: { type: 'string', default: 'text-center' },
        bbTimeZone: { type: 'string', default: '' },
    },

    edit: (props) => {
        const { attributes, setAttributes } = props;
        const { selectedBuddyBot, customClass, align, bbTimeZone } = attributes;

        const [buddyBots, setBuddyBots] = useState([]);
        const [isLoading, setIsLoading] = useState(true);
        const [botNotFound, setBotNotFound] = useState(false);
        const [isApiKeyMissing, setIsApiKeyMissing] = useState(false); // NEW STATE for API key check
        const hasShownEmptyNotice = useRef(false);

        useEffect(() => {
            const fetchBuddyBots = async () => {
                try {
                    const response = await wp.apiFetch({ path: '/buddybot/v1/buddybots' });
                    setBuddyBots(response);

                    if (selectedBuddyBot && !response.some(bot => bot.id === selectedBuddyBot)) {
                        setBotNotFound(true);
                    } else {
                        setBotNotFound(false);
                    }

                    if (Array.isArray(response) && response.length === 0 && !hasShownEmptyNotice.current) {
                        hasShownEmptyNotice.current = true;
                        dispatch('core/notices').createNotice(
                            'warning',
                            __('No BuddyBots found. Please create one in the BuddyBot dashboard.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                            {
                                isDismissible: true,
                                raw: true,
                                actions: [
                                    {
                                        label: __('Go to BuddyBot Dashboard', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                                        url: 'admin.php?page=buddybot-chatbot',
                                        external: false
                                    }
                                ]
                            }
                        );
                    }
                } catch (error) {
                    dispatch('core/notices').createNotice(
                        'error',
                        __('An error occurred while fetching BuddyBots: ', 'buddybot-ai-custom-ai-assistant-and-chat-agent') + error.message,
                        { isDismissible: true }
                    );
                } finally {
                    setIsLoading(false);
                }
            };

            const checkApiKey = async () => {
                try {
                    const apiResponse = await wp.apiFetch({ path: '/buddybot/v1/api-key-status' });
                    if (!apiResponse.apiKeyExists) {
                        setIsApiKeyMissing(true);
                    } else {
                        setIsApiKeyMissing(false);
                    }
                } catch (error) {
                    dispatch('core/notices').createNotice(
                        'error',
                        __('Error checking API key: ', 'buddybot-ai-custom-ai-assistant-and-chat-agent') + error,
                        { isDismissible: true }
                    );
                }
            };

            fetchBuddyBots();
            checkApiKey();
            setAttributes({ bbTimeZone: Intl.DateTimeFormat().resolvedOptions().timeZone });
        }, [selectedBuddyBot]);

        const options = [
            { label: __('Select a BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), value: '' },
            ...(Array.isArray(buddyBots) ? buddyBots.map((bot) => ({
                label: bot.chatbot_name || 'Unnamed BuddyBot',
                value: bot.id,
            })) : []),
        ];

        const alignmentOptions = [
            { icon: 'editor-alignleft', value: 'buddybot-msg-wrap buddybot-align-items-left' },
            { icon: 'editor-aligncenter', value: 'buddybot-msg-wrap buddybot-align-items-center' },
            { icon: 'editor-alignright', value: 'buddybot-msg-wrap buddybot-align-items-right' },
            { icon: 'editor-justify', value: '' },
        ];


        return createElement('div', useBlockProps(),
            createElement(BlockControls, {},
                createElement(AlignmentToolbar, {
                    value: align,
                    onChange: (value) => setAttributes({ align: value || 'text-left' }),
                })
            ),
            createElement(InspectorControls, {},
                createElement(PanelBody, { title: __('BuddyBot Settings', 'buddybot-ai-custom-ai-assistant-and-chat-agent') },
                    isLoading ? createElement(Spinner) :
                        createElement(SelectControl, {
                            __next40pxDefaultSize: true, 
                            label: __('Select BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                            value: selectedBuddyBot,
                            options: options,
                            onChange: (value) => setAttributes({ selectedBuddyBot: value }),
                            disabled: isApiKeyMissing,
                            __nextHasNoMarginBottom: true,
                        }),
                    createElement(TextControl, {
                        __next40pxDefaultSize: true, 
                        label: __('Custom CSS Class', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                        value: customClass,
                        onChange: (value) => setAttributes({ customClass: value }),
                        __nextHasNoMarginBottom: true,
                    }),

                    // createElement('div', { style: { marginTop: '10px' } },
                    //     createElement('p', {}, __('Align', 'buddybot-ai-custom-ai-assistant-and-chat-agent')),
                    //     createElement(ButtonGroup, {},
                    //         alignmentOptions.map(({ icon, value }) =>
                    //             createElement(Button, {
                    //                 isPrimary: align === value,
                    //                 isSecondary: align !== value,
                    //                 onClick: () => setAttributes({ align: value }),
                    //             },
                    //                 createElement(Dashicon, { icon })
                    //             )
                    //         )
                    //     )
                    // ),

                    // Sidebar warning for no BuddyBot selected
                    !selectedBuddyBot && createElement('div', { style: { marginTop: '20px' } },
                        createElement(Notice, {
                            status: 'warning',
                            isDismissible: false,
                        }, __('Please select a BuddyBot to display.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'))
                    )
                )
            ),



            createElement('div', { className: `${botNotFound || !selectedBuddyBot || isApiKeyMissing ? 'buddybot-block-container' : ''} ${customClass || ''} ${align}` },

                // Show warning if API key is missing
                isApiKeyMissing &&
                createElement(Notice, {
                    status: 'warning',
                    isDismissible: false
                }, __('API key not configured. BuddyBot may not work correctly on frontend.', 'buddybot-ai-custom-ai-assistant-and-chat-agent')),

                // Show warning if BuddyBot is deleted
                botNotFound &&
                createElement(Notice, {
                    status: 'warning',
                    isDismissible: false
                }, __('The selected BuddyBot no longer exists. Please select a different one.', 'buddybot-ai-custom-ai-assistant-and-chat-agent')),

                // Show dropdown in both cases, but disable when API key is missing
                (botNotFound || !selectedBuddyBot || isApiKeyMissing) ?
                    createElement('div', { className: 'buddybot-block-content' },
                        createElement('div', { className: 'buddybot-mb-3 buddybot-fs-3 buddybot-text-dark'}, __('BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent')),

                        isLoading ? createElement(Spinner) :
                        createElement(SelectControl, {
                            value: selectedBuddyBot,
                            options: options,
                            onChange: (value) => setAttributes({ selectedBuddyBot: value }),
                            __nextHasNoMarginBottom: true,
                            disabled: isApiKeyMissing,
                        })
                    ) :
                    createElement(wp.serverSideRender, {
                        block: 'buddybot/chat',
                        attributes: { selectedBuddyBot, bbTimeZone, align, customClass },
                    })
            )
        );
    },

    save: () => null, // Uses ServerSideRender for dynamic content
});
});

