// ─── Help Me Write — Gmail-style AI flow ────────────────────────────────────
// State
var _hmwContext = '';
var _hmwFieldId = '';
var _hmwFieldType = '';
var _hmwText = '';
var _hmwOriginalText = ''; // always the first generated result — elaborate uses this to avoid snowballing

// Inject modal into <body> once so it is never trapped inside a hidden tab-pane
$(function () {
    if ($('#hmwModal').length) return; // already injected

    $('body').append(
        '<div id="hmwModal" style="display:none;position:fixed;inset:0;z-index:99999;align-items:center;justify-content:center;">' +
        '<div style="position:absolute;inset:0;background:rgba(0,0,0,0.45);" onclick="hmwClose()"></div>' +
        '<div id="hmwPanel" style="position:relative;width:100%;max-width:480px;background:#fff;border-radius:16px;box-shadow:0 8px 40px rgba(0,0,0,0.22);padding:0;overflow:visible;margin:16px;">' +
        '<div style="background:linear-gradient(270deg, #e0d7ff, #c8e6ff, #d4f1f9, #e8d5f5, #e0d7ff);;background-size:300% 300%;animation:aiGradientMoveBtn 4s ease infinite;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-radius:16px 16px 0 0;">' +
        '<div style="display:flex;align-items:center;gap:8px;color:#5a3e9e;font-weight:700;font-size:15px;">' +
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="#5a3e9e"><path d="M12 3c-1.2 5.4-5.4 7.8-9 9 3.6 1.2 7.8 3.6 9 9 1.2-5.4 5.4-7.8 9-9-3.6-1.2-7.8-3.6-9-9z"/><path d="M5 3c-.6 2.7-2.3 3.7-4 4 1.7.3 3.4 1.3 4 4 .6-2.7 2.3-3.7 4-4-1.7-.3-3.4-1.3-4-4z" opacity=".8"/></svg>' +
        'Help me write' +
        '</div>' +
        '<button onclick="hmwClose()" style="background:none;border:none;color:#5a3e9e;font-size:20px;cursor:pointer;line-height:1;padding:0;">&times;</button>' +
        '</div>' +

        '<div id="hmwStepPrompt" style="padding:20px;">' +
        '<label style="font-size:13px;font-weight:600;color:#444;margin-bottom:6px;display:block;">What do you want to write?</label>' +
        '<textarea id="hmwPromptInput" rows="3" placeholder="e.g. Write a follow-up note, translate to Spanish, summarize this, convert to formal English..." style="width:100%;border:1.5px solid #ddd;border-radius:10px;padding:10px;font-size:13px;resize:none;outline:none;transition:border .2s;" onfocus="this.style.borderColor=\'#6c5ce7\'" onblur="this.style.borderColor=\'#ddd\'"></textarea>' +
        '<span id="hmwPromptError" style="color:#e74c3c;font-size:12px;display:none;">Please describe what you want to write.</span>' +
        '<div style="display:flex;gap:8px;margin-top:14px;justify-content:flex-end;">' +
        '<button onclick="hmwClose()" style="padding:8px 18px;border-radius:20px;border:1.5px solid #ddd;background:#fff;font-size:13px;cursor:pointer;color:#555;">Cancel</button>' +
        '<button onclick="hmwGenerate()" id="hmwCreateBtn" style="padding:8px 22px;border-radius:20px;border:none;background-image:linear-gradient(270deg,#6c5ce7,#0984e3,#00b894,#6c5ce7);background-size:300% 300%;animation:aiGradientMoveBtn 4s ease infinite;color:#fff;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 3px 12px rgba(108,92,231,0.35);">' +
        '<svg width="13" height="13" viewBox="0 0 24 24" fill="white" style="vertical-align:middle;margin-right:4px;"><path d="M12 3c-1.2 5.4-5.4 7.8-9 9 3.6 1.2 7.8 3.6 9 9 1.2-5.4 5.4-7.8 9-9-3.6-1.2-7.8-3.6-9-9z"/></svg>' +
        'Create' +
        '</button>' +
        '</div>' +
        '</div>' +

        '<div id="hmwStepPreview" style="padding:20px;display:none;">' +
        '<label style="font-size:12px;font-weight:600;color:#888;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;display:block;">Generated text</label>' +
        '<div id="hmwPreviewText" style="background:#f8f7ff;border:1.5px solid #e0dcff;border-radius:10px;padding:12px;font-size:13px;color:#333;line-height:1.6;min-height:60px;white-space:pre-wrap;max-height:200px;overflow-y:auto;"></div>' +
        '<div style="display:flex;gap:8px;margin-top:14px;flex-wrap:wrap;align-items:center;">' +
        '<button onclick="hmwInsert()" style="padding:8px 18px;border-radius:20px;border:none;background-image:linear-gradient(270deg,#6c5ce7,#0984e3,#00b894,#6c5ce7);background-size:300% 300%;animation:aiGradientMoveBtn 4s ease infinite;color:#fff;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px;box-shadow:0 3px 12px rgba(108,92,231,0.35);">' +
        '<svg width="13" height="13" viewBox="0 0 24 24" fill="white"><path d="M19 11H7.83l4.88-4.88c.39-.39.39-1.03 0-1.42-.39-.39-1.02-.39-1.41 0l-6.59 6.59c-.39.39-.39 1.02 0 1.41l6.59 6.59c.39.39 1.02.39 1.41 0 .39-.39.39-1.02 0-1.41L7.83 13H19c.55 0 1-.45 1-1s-.45-1-1-1z"/></svg>' +
        'Insert' +
        '</button>' +
        '<button onclick="hmwRefine(\'recreate\')" style="padding:8px 18px;border-radius:20px;border:1.5px solid #6c5ce7;background:#fff;color:#6c5ce7;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px;">' +
        '<svg width="13" height="13" viewBox="0 0 24 24" fill="#6c5ce7"><path d="M17.65 6.35A7.958 7.958 0 0 0 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08A5.99 5.99 0 0 1 12 18c-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg>' +
        'Recreate' +
        '</button>' +
        '<div style="position:relative;">' +
        '<button onclick="hmwToggleRefineMenu()" id="hmwRefineMenuBtn" style="padding:8px 18px;border-radius:20px;border:1.5px solid #ddd;background:#fff;color:#555;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px;">' +
        'Refine <svg width="12" height="12" viewBox="0 0 24 24" fill="#555"><path d="M7 10l5 5 5-5z"/></svg>' +
        '</button>' +
        '<div id="hmwRefineMenu" style="display:none;position:absolute;bottom:calc(100% + 6px);left:0;background:#fff;border:1px solid #eee;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.15);min-width:150px;overflow:hidden;z-index:100000;">' +
        '<button onclick="hmwRefine(\'formalize\')" class="hmw-refine-item" style="display:block;width:100%;padding:10px 16px;background:none;border:none;text-align:left;font-size:13px;color:#333;cursor:pointer;">📋 Formalize</button>' +
        '<button onclick="hmwRefine(\'shorten\')"   class="hmw-refine-item" style="display:block;width:100%;padding:10px 16px;background:none;border:none;text-align:left;font-size:13px;color:#333;cursor:pointer;">✂️ Shorten</button>' +
        '<button onclick="hmwRefine(\'elaborate\')" class="hmw-refine-item" style="display:block;width:100%;padding:10px 16px;background:none;border:none;text-align:left;font-size:13px;color:#333;cursor:pointer;">📝 Elaborate</button>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div style="margin-top:12px;">' +
        '<button onclick="hmwBack()" style="background:none;border:none;color:#6c5ce7;font-size:12px;cursor:pointer;padding:0;">← Edit prompt</button>' +
        '</div>' +
        '</div>' +

        '<div id="hmwStepLoading" style="padding:40px;display:none;text-align:center;">' +
        '<div style="width:40px;height:40px;border:3px solid #e0dcff;border-top-color:#6c5ce7;border-radius:50%;animation:hmwSpin 0.8s linear infinite;margin:0 auto 12px;"></div>' +
        '<div style="color:#888;font-size:13px;" id="hmwLoadingText">Generating...</div>' +
        '</div>' +
        '</div>' +
        '</div>'
    );

    $('[data-toggle="tooltip"][data-hmw-context]').tooltip({ placement: 'top', trigger: 'hover' });
});

// ── Open modal ────────────────────────────────────────────────────────────────
function aiHelpMeWrite(context, fieldId, fieldType) {
    _hmwContext = context;
    _hmwFieldId = fieldId;
    _hmwFieldType = fieldType;
    _hmwText = '';
    _hmwOriginalText = '';

    $('#hmwPromptInput').val('');
    $('#hmwPromptError').hide();
    _hmwShowStep('prompt');
    $('#hmwModal').css('display', 'flex');

    setTimeout(function () { $('#hmwPromptInput').focus(); }, 100);
}

// ── Close modal ───────────────────────────────────────────────────────────────
function hmwClose() {
    $('#hmwModal').hide();
    $('#hmwRefineMenu').hide();
}

// ── Back to prompt step ───────────────────────────────────────────────────────
function hmwBack() {
    $('#hmwRefineMenu').hide();
    _hmwShowStep('prompt');
}

// ── Show step helper ──────────────────────────────────────────────────────────
function _hmwShowStep(step) {
    $('#hmwStepPrompt').hide();
    $('#hmwStepPreview').hide();
    $('#hmwStepLoading').hide();
    if (step === 'prompt') $('#hmwStepPrompt').show();
    if (step === 'preview') $('#hmwStepPreview').show();
    if (step === 'loading') $('#hmwStepLoading').show();
}

// ── Generate from prompt ──────────────────────────────────────────────────────
function hmwGenerate() {
    var prompt = $('#hmwPromptInput').val().trim();
    if (!prompt) {
        $('#hmwPromptError').show();
        return;
    }
    $('#hmwPromptError').hide();
    $('#hmwLoadingText').text('Generating...');
    _hmwShowStep('loading');

    $.ajax({
        type: 'POST',
        url: _HELP_ME_WRITE_URL,
        data: { _token: _CSRF_TOKEN, prompt: prompt, context: _hmwContext, record_id: (typeof _RECORD_ID !== 'undefined' ? _RECORD_ID : '') },
        success: function (res) {
            if (res.success && res.data) {
                _hmwText = res.data;
                _hmwOriginalText = res.data; // lock the baseline for elaborate
                $('#hmwPreviewText').text(res.data);
                _hmwShowStep('preview');
            } else {
                toastr.error(res.error || 'AI could not generate text.');
                _hmwShowStep('prompt');
            }
        },
        error: function (xhr) {
            showErrorAndLoginRedirection(xhr);
            _hmwShowStep('prompt');
        }
    });
}

// ── Insert into field ─────────────────────────────────────────────────────────
function hmwInsert() {
    if (!_hmwText) return;
    var $field = $('#' + _hmwFieldId);
    if (_hmwFieldType === 'contenteditable') {
        var escaped = $('<div>').text(_hmwText).html();
        var withBreaks = escaped.replace(/\n/g, '<br>');
        $field.html(withBreaks);
        $field.removeClass('h-25').css({
            'height'    : 'auto',
            'min-height': '50px',
            'max-height': 'none',
            'overflow-y': 'auto'
        });
    } else {
        $field.val(_hmwText);
        $field.css({ 'height': 'auto', 'max-height': 'none' });
        $field.css('height', $field[0].scrollHeight + 'px');
    }
    hmwClose();
}

// ── Refine (formalize / shorten / elaborate / recreate) ───────────────────────
function hmwRefine(action) {
    $('#hmwRefineMenu').hide();
    var labels = { formalize: 'Formalizing...', shorten: 'Shortening...', elaborate: 'Elaborating...', recreate: 'Recreating...' };
    $('#hmwLoadingText').text(labels[action] || 'Refining...');
    _hmwShowStep('loading');

    // elaborate always works from the original generated text so repeated clicks don't snowball
    var textToRefine = (action === 'elaborate' && _hmwOriginalText) ? _hmwOriginalText : _hmwText;

    $.ajax({
        type: 'POST',
        url: _HELP_ME_WRITE_REFINE_URL,
        data: { _token: _CSRF_TOKEN, text: textToRefine, action: action, context: _hmwContext, record_id: (typeof _RECORD_ID !== 'undefined' ? _RECORD_ID : '') },
        success: function (res) {
            if (res.success && res.data) {
                _hmwText = res.data;
                $('#hmwPreviewText').text(res.data);
                _hmwShowStep('preview');
            } else {
                toastr.error(res.error || 'AI could not refine text.');
                _hmwShowStep('preview');
            }
        },
        error: function (xhr) {
            showErrorAndLoginRedirection(xhr);
            _hmwShowStep('preview');
        }
    });
}

// ── Toggle refine dropdown ────────────────────────────────────────────────────
function hmwToggleRefineMenu() {
    var $m = $('#hmwRefineMenu');
    $m.is(':visible') ? $m.hide() : $m.show();
}

// Close refine menu on outside click
$(document).on('click', function (e) {
    if (!$(e.target).closest('#hmwRefineMenuBtn, #hmwRefineMenu').length) {
        $('#hmwRefineMenu').hide();
    }
});

// Allow Enter key in prompt textarea to trigger Create
$(document).on('keydown', '#hmwPromptInput', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        hmwGenerate();
    }
});
