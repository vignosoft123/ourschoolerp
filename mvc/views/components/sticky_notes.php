<style>
/* ── Sticky Notes ──────────────────────────────────── */
#sticky-trigger {
    position: fixed;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    z-index: 1050;
    background: #f9a825;
    color: #fff;
    border: none;
    border-radius: 6px 0 0 6px;
    padding: 14px 9px;
    cursor: pointer;
    box-shadow: -3px 2px 10px rgba(0,0,0,0.25);
    font-size: 17px;
    line-height: 1;
    transition: background .2s;
}
#sticky-trigger:hover { background: #e65100; }
#sticky-drawer {
    position: fixed;
    right: -360px;
    top: 0;
    width: 360px;
    height: 100vh;
    background: #f5f5f5;
    z-index: 1200;
    box-shadow: -4px 0 24px rgba(0,0,0,0.18);
    transition: right 0.3s cubic-bezier(.4,0,.2,1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
#sticky-drawer.open { right: 0; }
#sticky-drawer-header {
    background: #f9a825;
    color: #fff;
    padding: 13px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    font-weight: 600;
    font-size: 14px;
    letter-spacing: .3px;
}
#sticky-drawer-header button {
    background: none;
    border: none;
    color: #fff;
    font-size: 20px;
    line-height: 1;
    cursor: pointer;
    padding: 0 2px;
    opacity: .85;
}
#sticky-drawer-header button:hover { opacity: 1; }
#sticky-drawer-toolbar {
    padding: 10px 12px;
    border-bottom: 1px solid #e0e0e0;
    flex-shrink: 0;
    background: #fff;
}
#sticky-notes-list {
    flex: 1;
    overflow-y: auto;
    padding: 10px 12px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.sticky-note-card {
    border-radius: 6px;
    padding: 10px 10px 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.10);
    position: relative;
    flex-shrink: 0;
}
.sticky-note-card textarea {
    width: 100%;
    border: none;
    background: transparent;
    resize: vertical;
    font-size: 13px;
    font-family: 'Segoe UI', sans-serif;
    min-height: 80px;
    outline: none;
    color: #333;
    padding: 0;
    line-height: 1.5;
    -webkit-user-select: text !important;
    -moz-user-select: text !important;
    -ms-user-select: text !important;
    user-select: text !important;
    pointer-events: auto !important;
    cursor: text;
}
#sticky-drawer, #sticky-notes-list {
    -webkit-user-select: text !important;
    -moz-user-select: text !important;
    user-select: text !important;
}
.sticky-note-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 6px;
}
.sticky-note-colors { display: flex; gap: 5px; }
.sticky-note-colors span {
    width: 16px; height: 16px; border-radius: 50%; cursor: pointer;
    border: 2px solid transparent; transition: border-color .15s;
}
.sticky-note-colors span:hover,
.sticky-note-colors span.active { border-color: #555; }
.sticky-note-actions { display: flex; align-items: center; gap: 6px; }
.sticky-note-saved { font-size: 10px; color: #777; }
.sticky-note-del {
    background: none; border: none; color: #e53935; font-size: 14px;
    cursor: pointer; padding: 0 2px; line-height: 1;
}
.sticky-note-del:hover { color: #b71c1c; }
</style>

<!-- ── Sticky Notes Trigger ─────────────────────────────────────────────────── -->
<button id="sticky-trigger" onclick="openStickyDrawer()" title="Sticky Notes">
    <i class="fa fa-thumb-tack"></i>
</button>

<!-- ── Sticky Notes Drawer ──────────────────────────────────────────────────── -->
<div id="sticky-drawer">
    <div id="sticky-drawer-header">
        <span><i class="fa fa-thumb-tack"></i>&nbsp; Sticky Notes</span>
        <button onclick="closeStickyDrawer()" title="Close">&times;</button>
    </div>
    <div id="sticky-drawer-toolbar">
        <button class="btn btn-sm btn-warning" onclick="addStickyNote()" style="width:100%;font-size:13px;">
            <i class="fa fa-plus"></i> Add Note
        </button>
    </div>
    <div id="sticky-notes-list"></div>
</div>

<script type="text/javascript">
var stickyDebounceTimers = {};
var STICKY_COLORS = ['#fff9c4','#fce4ec','#e3f2fd','#e8f5e9','#ede7f6'];

function openStickyDrawer() {
    document.getElementById('sticky-drawer').classList.add('open');
    if (document.getElementById('sticky-notes-list').children.length === 0) {
        loadStickyNotes();
    }
}

function closeStickyDrawer() {
    document.getElementById('sticky-drawer').classList.remove('open');
}

function loadStickyNotes() {
    var list = document.getElementById('sticky-notes-list');
    list.innerHTML = '<div style="text-align:center;padding:20px;color:#999;font-size:13px;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>';
    $.getJSON('<?=base_url("workspace/sticky_notes_get")?>', function(res) {
        list.innerHTML = '';
        if (res.notes && res.notes.length) {
            res.notes.forEach(function(n) { list.appendChild(buildNoteCard(n)); });
        } else {
            list.innerHTML = '<div style="text-align:center;color:#bbb;font-size:13px;padding:30px 0;"><i class="fa fa-thumb-tack" style="font-size:28px;display:block;margin-bottom:8px;"></i>No notes yet.<br>Click + Add Note to start.</div>';
        }
    });
}

function buildNoteCard(note) {
    var card = document.createElement('div');
    card.className = 'sticky-note-card';
    card.dataset.id = note.id;
    card.style.background = note.color || '#fff9c4';

    var colorDots = STICKY_COLORS.map(function(c) {
        return '<span style="background:' + c + ';" class="' + (note.color === c ? 'active' : '') + '" onclick="changeNoteColor(' + note.id + ', \'' + c + '\', this)" title="' + c + '"></span>';
    }).join('');

    card.innerHTML =
        '<textarea data-id="' + note.id + '" oninput="autoSaveNote(' + note.id + ', this)">' + $('<div>').text(note.note || '').html() + '</textarea>' +
        '<div class="sticky-note-footer">' +
            '<div class="sticky-note-colors">' + colorDots + '</div>' +
            '<div class="sticky-note-actions">' +
                '<span class="sticky-note-saved" id="note-saved-' + note.id + '"></span>' +
                '<button class="sticky-note-del" onclick="deleteStickyNote(' + note.id + ', this)" title="Delete note"><i class="fa fa-trash"></i></button>' +
            '</div>' +
        '</div>';
    return card;
}

function addStickyNote() {
    $.post('<?=base_url("workspace/sticky_notes_save")?>', { note: '', color: '#fff9c4' }, function(res) {
        if (!res.success) return;
        var list = document.getElementById('sticky-notes-list');
        if (list.querySelector('div[style*="text-align:center"]')) list.innerHTML = '';
        var card = buildNoteCard({ id: res.id, note: '', color: '#fff9c4' });
        list.insertBefore(card, list.firstChild);
        card.querySelector('textarea').focus();
    }, 'json');
}

function autoSaveNote(id, ta) {
    clearTimeout(stickyDebounceTimers[id]);
    var savedEl = document.getElementById('note-saved-' + id);
    if (savedEl) savedEl.textContent = '';
    stickyDebounceTimers[id] = setTimeout(function() {
        var color = ta.closest('.sticky-note-card').style.background;
        $.post('<?=base_url("workspace/sticky_notes_save")?>', { id: id, note: ta.value, color: color }, function(res) {
            if (savedEl && res.success) { savedEl.textContent = '✓ saved'; setTimeout(function(){ if(savedEl) savedEl.textContent=''; }, 1500); }
        }, 'json');
    }, 800);
}

function changeNoteColor(id, color, el) {
    var card = el.closest('.sticky-note-card');
    card.style.background = color;
    card.querySelectorAll('.sticky-note-colors span').forEach(function(s){ s.classList.remove('active'); });
    el.classList.add('active');
    var ta = card.querySelector('textarea');
    $.post('<?=base_url("workspace/sticky_notes_save")?>', { id: id, note: ta.value, color: color }, null, 'json');
}

function deleteStickyNote(id, btn) {
    if (!confirm('Delete this note?')) return;
    $.post('<?=base_url("workspace/sticky_notes_delete")?>', { id: id }, function(res) {
        if (res.success) {
            var card = btn.closest('.sticky-note-card');
            card.remove();
            if (document.getElementById('sticky-notes-list').children.length === 0) {
                document.getElementById('sticky-notes-list').innerHTML =
                    '<div style="text-align:center;color:#bbb;font-size:13px;padding:30px 0;"><i class="fa fa-thumb-tack" style="font-size:28px;display:block;margin-bottom:8px;"></i>No notes yet.<br>Click + Add Note to start.</div>';
            }
        }
    }, 'json');
}

document.addEventListener('click', function(e) {
    var drawer = document.getElementById('sticky-drawer');
    var trigger = document.getElementById('sticky-trigger');
    if (drawer.classList.contains('open') && !drawer.contains(e.target) && e.target !== trigger && !trigger.contains(e.target)) {
        closeStickyDrawer();
    }
});
</script>
