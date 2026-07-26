// https://www.tiny.cloud/docs/ui-components/dialogcomponents/#basiccomponents
var AddVideo = {
    title: 'Embed video',
    body: {
        type: 'panel',
        items: [
            {
                type: 'selectbox',
                name: 'type',
                label: 'Select video provider',
                size: 1,
                items: [
                    {value: 'youtube', text: 'YouTube'},
                    {value: 'vimeo', text: 'Vimeo'},
                    {value: 'dailymotion', text: 'Dailymotion'},
                    {value: 'tiktok', text: 'TikTok'}
                ]
            },
            {
                type: 'input',
                name: 'url',
                label: 'Enter the video URL'
            }
        ]
    },
    buttons: [
        {
            type: 'cancel',
            name: 'closeButton',
            text: 'Cancel'
        },
        {
            type: 'submit',
            name: 'submitButton',
            text: 'Add',
            primary: true
        }
    ],
    initialData: {
        type: '',
        url: ''
    },
    onSubmit: function (api) {
        var data = api.getData();

        tinymce.activeEditor.execCommand('mceInsertContent', false, '[embed_video type="' + data.type + '"]' + data.url + '[/embed_video]');
        api.close();
    }
};

var AddAudio = {
    title: 'Embed audio',
    body: {
        type: 'panel',
        items: [
            {
                type: 'selectbox',
                name: 'type',
                label: 'Select audio provider',
                size: 1,
                items: [
                    {value: 'soundcloud', text: 'SoundCloud'}
                ]
            },
            {
                type: 'input',
                name: 'url',
                label: 'Enter the audio URL'
            }
        ]
    },
    buttons: [
        {
            type: 'cancel',
            name: 'closeButton',
            text: 'Cancel'
        },
        {
            type: 'submit',
            name: 'submitButton',
            text: 'Add',
            primary: true
        }
    ],
    initialData: {
        type: '',
        url: ''
    },
    onSubmit: function (api) {
        var data = api.getData();

        tinymce.activeEditor.execCommand('mceInsertContent', false, '[embed_audio type="' + data.type + '"]' + data.url + '[/embed_audio]');
        api.close();
    }
};

var AddFacebook = {
    title: 'Embed Facebook post/image/video',
    body: {
        type: 'panel',
        items: [
            {
                type: 'input',
                name: 'url',
                label: 'Enter the post/image/video URL'
            }
        ]
    },
    buttons: [
        {
            type: 'cancel',
            name: 'closeButton',
            text: 'Cancel'
        },
        {
            type: 'submit',
            name: 'submitButton',
            text: 'Add',
            primary: true
        }
    ],
    initialData: {
        type: '',
        url: ''
    },
    onSubmit: function (api) {
        var data = api.getData();

        tinymce.activeEditor.execCommand('mceInsertContent', false, '[embed_facebook]' + data.url + '[/embed_facebook]');
        api.close();
    }
};

var AddTwitter = {
    title: 'Embed Twitter post',
    body: {
        type: 'panel',
        items: [
            {
                type: 'input',
                name: 'url',
                label: 'Enter the URL'
            }
        ]
    },
    buttons: [
        {
            type: 'cancel',
            name: 'closeButton',
            text: 'Cancel'
        },
        {
            type: 'submit',
            name: 'submitButton',
            text: 'Add',
            primary: true
        }
    ],
    initialData: {
        type: '',
        url: ''
    },
    onSubmit: function (api) {
        var data = api.getData();

        tinymce.activeEditor.execCommand('mceInsertContent', false, '[embed_twitter]' + data.url + '[/embed_twitter]');
        api.close();
    }
};

var AddInstagram = {
    title: 'Embed Instagram',
    body: {
        type: 'panel',
        items: [
            {
                type: 'input',
                name: 'url',
                label: 'Enter the URL'
            }
        ]
    },
    buttons: [
        {
            type: 'cancel',
            name: 'closeButton',
            text: 'Cancel'
        },
        {
            type: 'submit',
            name: 'submitButton',
            text: 'Add',
            primary: true
        }
    ],
    initialData: {
        type: '',
        url: ''
    },
    onSubmit: function (api) {
        var data = api.getData();

        tinymce.activeEditor.execCommand('mceInsertContent', false, '[embed_instagram]' + data.url + '[/embed_instagram]');
        api.close();
    }
};

var AddPinterest = {
    title: 'Embed Pinterest',
    body: {
        type: 'panel',
        items: [
            {
                type: 'input',
                name: 'url',
                label: 'Enter the URL'
            }
        ]
    },
    buttons: [
        {
            type: 'cancel',
            name: 'closeButton',
            text: 'Cancel'
        },
        {
            type: 'submit',
            name: 'submitButton',
            text: 'Add',
            primary: true
        }
    ],
    initialData: {
        type: '',
        url: ''
    },
    onSubmit: function (api) {
        var data = api.getData();

        tinymce.activeEditor.execCommand('mceInsertContent', false, '[embed_pinterest]' + data.url + '[/embed_pinterest]');
        api.close();
    }
};

var AddGist = {
    title: 'Embed',
    body: {
        type: 'panel',
        items: [
            {
                type: 'input',
                name: 'url',
                label: 'Enter the gist URL'
            }
        ]
    },
    buttons: [
        {
            type: 'cancel',
            name: 'closeButton',
            text: 'Cancel'
        },
        {
            type: 'submit',
            name: 'submitButton',
            text: 'Add',
            primary: true
        }
    ],
    initialData: {
        type: '',
        url: ''
    },
    onSubmit: function (api) {
        var data = api.getData();

        tinymce.activeEditor.execCommand('mceInsertContent', false, '[embed_github_gist]' + data.url + '[/embed_github_gist]');
        api.close();
    }
};

var AddGeneral = {
    title: 'Embed',
    body: {
        type: 'panel',
        items: [
            {
                type: 'input',
                name: 'url',
                label: 'Enter the URL'
            }
        ]
    },
    buttons: [
        {
            type: 'cancel',
            name: 'closeButton',
            text: 'Cancel'
        },
        {
            type: 'submit',
            name: 'submitButton',
            text: 'Add',
            primary: true
        }
    ],
    initialData: {
        type: '',
        url: ''
    },
    onSubmit: function (api) {
        var data = api.getData();

        tinymce.activeEditor.execCommand('mceInsertContent', false, '[embed_general]' + data.url + '[/embed_general]');
        api.close();
    }
};
