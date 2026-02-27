/**
 * Menu Item Cascade Dropdown Handler
 * Menü Öğesi Kademeli Açılır Liste Yöneticisi
 * * Location: /src/assets/apps/custom/
 */
$(document).ready(function () {
    
    // Maps the HTML IDs to a local object for easier access
    const selectors = {
        module: '#module-list',
        type: '#routeType-list',
        route: '#route-list',
        model: '#model-list'
    };

    
    // Tracks request IDs to prevent "Race Conditions" and stores active AJAX objects
    const state = {
        requests: { type: 0, route: 0, model: 0 },
        activeReqs: { type: null, route: null, model: null }
    };

   
    // Status and prompt messages for different dropdown states
    const messages = {
        type: { default: 'Tip Seçin', wait: 'Önce Modül Seçin', load: 'Yükleniyor...' },
        route: { default: 'Rota Seçin', wait: 'Önce Tip Seçin', load: 'Yükleniyor...' },
        model: { default: 'Model Seçin', wait: 'Önce Rota Seçin', load: 'Yükleniyor...' }
    };

   
    const ui = {
        // Disables the dropdown and shows a placeholder message
        lock: (key, msg) => {
            $(selectors[key]).empty().append(`<option value="">${msg}</option>`).prop('disabled', true);
        },
        
        // Dropdown'ı temizler ve sunucudan gelen yeni verilerle doldurur
        fill: (key, data) => {
            const $el = $(selectors[key]);
            $el.empty().append(`<option value="">${messages[key].default}</option>`);
            if (data && data.length > 0) {
                // Use new Option() for cleaner DOM insertion
                data.forEach(item => $el.append(new Option(item.name, item.id)));
                $el.prop('disabled', false);
            }
        }
    };

    
    // Handles all server requests, security tokens, and race condition logic
    const fetchData = (key, url, payload, onSuccess) => {
        // Increment request ID for this specific dropdown
        const currentId = ++state.requests[key];

        // Abort previous unfinished request for the same dropdown
        if (state.activeReqs[key]) state.activeReqs[key].abort();

        ui.lock(key, messages[key].load);

        state.activeReqs[key] = $.ajax({
            url: url,
            type: 'POST',
            // Merges payload with CSRF token for security
            data: { ...payload, [yii.getCsrfParam()]: yii.getCsrfToken() },
            success: (data) => {
                // Only process the data if it's the latest request
                if (currentId === state.requests[key]) {
                    onSuccess(data);
                }
            },
            error: (xhr) => {
                // Ignore "abort" errors, handle real connection issues
                if (xhr.statusText !== 'abort') {
                    ui.lock(key, 'Hata oluştu!');
                }
            }
        });
    };



    // Triggered when Module changes
    $(document).on('change', selectors.module, function () {
        const moduleName = $(this).val();

        ui.lock('route', messages.route.wait);
        ui.lock('model', messages.model.wait);

        if (!moduleName) {
            ui.lock('type', messages.type.default);
            return;
        }

        fetchData('type', '/menu/item/route-type', { moduleName }, (data) => ui.fill('type', data));
    });

    // Triggered when Type changes
    $(document).on('change', selectors.type, function () {
        const type = $(this).val();
        const module = $(selectors.module).val();

        ui.lock('model', messages.model.wait);

        if (!type) {
            ui.lock('route', messages.route.default);
            return;
        }

        fetchData('route', '/menu/item/route', { module, type }, (data) => ui.fill('route', data));
    });

    // Triggered when Route changes 
    $(document).on('change', selectors.route, function () {
        const route = $(this).val();
        const type = $(selectors.type).val();
        const module = $(selectors.module).val();

        // Model dropdown is only needed if type is 'model'
        if (!route || type !== 'model') {
            ui.lock('model', messages.model.default);
            return;
        }

        fetchData('model', '/menu/item/model', { module, type, route }, (data) => ui.fill('model', data));
    });
});