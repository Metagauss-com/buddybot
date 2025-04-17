document.addEventListener("DOMContentLoaded", function () {
const { registerBlockType } = wp.blocks;
const { createElement, useState, useEffect, useRef } = wp.element;
const { __ } = wp.i18n;
const { InspectorControls, useBlockProps, BlockControls, AlignmentToolbar } = wp.blockEditor;
const { TextControl, SelectControl, PanelBody, Spinner, ButtonGroup, Button, Dashicon, Notice } = wp.components;
const { dispatch } = wp.data;
const el = wp.element.createElement;

const BuddyBotIcon = createElement(
  'svg',
  { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 1024 1024' },
  createElement('path', {
    fill: 'currentColor',
    d: 'M552.07 812.32c-12.3.67-24.11 1.11-35.94.93-26.3-.39-52.6-.95-78.81-3.07-38.35-3.04-76.34-8.39-112.56-22.22-59.46-22.7-95.85-65.94-111.91-126.92-1.45-5.5-2.75-8.71-9.56-9.33-25.61-2.32-41.76-21.6-46-42.08a120.3 120.3 0 0 1-1.86-21.36v-82.4c.22-27.33 17.46-49.61 41.31-55.6 3.68-.92 7.53-1.6 11.31-1.58 3.94.02 5.55-1.56 6.51-5.21 5.82-21.97 15.2-42.26 29.02-60.36 26.54-34.89 62.24-55.49 104.06-66.5 22.74-5.98 45.9-9.19 69.29-11.18 20.73-1.76 41.46-3 62.28-2.99 10.93 0 11.36-.99 12.26-11.77l4.47-45.6c.8-7.26 1.52-14.52 2.43-21.76.67-5.3 1.36-10.63 2.1-15.94.57-4.37-.97-6.96-4.77-9.3-18.03-11.23-25.66-33.23-18.81-53.21 6.08-17.69 21.2-28.39 39.9-28.21 16.57.17 31.86 12.06 37.33 29.04 6.71 20.83-.99 41.73-19.78 53.05-2.63 1.58-4.55 3-4.17 6.49l5.07 48.64c.83 8.08 1.62 16.16 2.19 24.26.29 4.3 1.66 6.34 6.52 6.76 14.95 1.3 29.95.65 44.9 1.73 38.16 2.83 76.42 5.29 112.64 19.24 51.42 19.73 89.24 53.56 108.43 106.25 1.87 5.15 3.36 10.42 4.82 15.73.78 2.84 2.16 3.99 5.26 3.97 30.85-.21 54.18 25.02 54.45 56.08.24 26.99.18 53.99.03 80.99-.19 33.6-25.32 53.77-47.75 54.73-5.48.23-8.29 2.57-9.61 8.41-4.76 21.09-12.6 41.02-24.43 59.26-19.86 30.65-47.54 51.74-80.61 66.31-27.36 12.05-56.26 18.2-85.74 21.89-23.78 2.95-47.65 4.72-72.1 5.04zm28.22-386c-7.66-.04-15.31-.78-22.94-.83-50.43-.34-100.89-1.77-151.15 3.81-21.36 2.37-42.48 5.74-62.69 13.36-25.71 9.7-44.09 26.94-53.53 53.03-6.15 17.01-8.71 34.81-9.49 52.76-.86 19.96.32 39.94 4.36 59.61 4.27 20.84 12.25 39.69 28.12 54.55 14.61 13.69 32.67 20.36 51.56 24.93 38.19 9.26 77.29 10.91 116.3 11.92 44.58 1.16 89.18.86 133.63-3.59 21.51-2.2 42.84-5.38 63.49-12.15 33.18-10.92 55.34-32.58 63.86-66.88 7.47-30.08 7.75-60.58 3.15-91.06-2.5-16.53-7.17-32.49-16.36-46.8-14.02-21.84-34.83-33.76-59.06-40.69-28.67-8.22-58.2-10.06-88.7-11.91z'
  }),
  createElement('path', {
    fill: 'currentColor',
    d: 'M619.59 507.2c31.54-5.32 50.36 15.1 55.56 40.62 3.35 16.42 1.26 32.44-7.35 47.18-17.74 30.28-60.11 29.48-76.82 0-13.73-24.24-10.34-59.33 8.63-77.33 5.56-5.27 12.03-8.82 20-10.47z'
  }),
  createElement('path', {
    fill: 'currentColor',
    d: 'M445.31 554.1c.97 14.46-.65 27.83-7.74 40.41-15.65 27.86-52.56 31.17-72.41 6.24-17.64-22.15-17.2-58.42 1-80.15 15.83-18.95 48.61-19.07 64.67 0 8.22 9.65 12.97 20.8 14.48 33.5z'
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
                        }, __('No BuddyBot selected. Choose one from the sidebar to enable preview and publishing.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'))
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
                            __next40pxDefaultSize: true,
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

