/**
 * Menu Item Cascading Dropdown Handler:
 * Manages the asynchronous dependency logic between Module, Type, Route, and Model dropdowns.
 * Location: /src/assets/apps/custom/
 */

$(document).ready(function () {

    /**
     * DOM Selector Configuration:
     * Maps the specific HTML element IDs to a centralized configuration object.
     */

    const selectors = {
        module: '#module-list',
        type: '#routeType-list',
        route: '#route-list',
        model: '#model-list'
    };



    /**
     * Request State Management:
     * Tracks asynchronous request IDs to mitigate "Race Conditions" and maintains
     * active AJAX references for request cancellation (abort).
     */

    const state = {
        requests: { type: 0, route: 0, model: 0 },
        activeReqs: { type: null, route: null, model: null }
    };



    /**
     * Localized Status Messages:
     * Defines the prompt and status labels for various dropdown states.
     */

    const messages = {
        type: { default: 'Tip Seçin', wait: 'Önce Modül Seçin', load: 'Yükleniyor...' },
        route: { default: 'Rota Seçin', wait: 'Önce Tip Seçin', load: 'Yükleniyor...' },
        model: { default: 'Model Seçin', wait: 'Önce Rota Seçin', load: 'Yükleniyor...' }
    };



    /**
     * UI Manipulation Helpers:
     * Abstracted functions for updating the DOM state of the dropdown elements.
     */

    const ui = {
        // Disables the dropdown and displays a contextual placeholder message.
        lock: (key, msg) => {
            $(selectors[key]).empty().append(`<option value="">${msg}</option>`).prop('disabled', true);
        },

       

        // Populates the dropdown with server-side data and restores interactive state.
        fill: (key, data) => {
            const $el = $(selectors[key]);
            $el.empty().append(`<option value="">${messages[key].default}</option>`);
            if (data && data.length > 0) {
                // Efficient DOM insertion using the native Option constructor.
                data.forEach(item => $el.append(new Option(item.name, item.id)));
                $el.prop('disabled', false);
            }

        }

    };



    /**
     * Asynchronous Data Fetcher:
     * Handles the server-side request lifecycle, including CSRF token injection,
     * stale request cancellation, and sequence validation.
     */

    const fetchData = (key, url, payload, onSuccess) => {
        // Increment the request sequence ID for the specific dropdown target.
        const currentId = ++state.requests[key];
        // Abort any existing pending request for the same target to optimize network load.
        if (state.activeReqs[key]) state.activeReqs[key].abort();
        ui.lock(key, messages[key].load);

        state.activeReqs[key] = $.ajax({
            url: url,
            type: 'POST',
            // Injects security tokens required by the Portalium framework.
            data: { ...payload, [yii.getCsrfParam()]: yii.getCsrfToken() },
            success: (data) => {
                // Only execute the callback if this response matches the most recent request sequence.
                if (currentId === state.requests[key]) {
                    onSuccess(data);
                }

            },

            error: (xhr) => {
                // Silently ignore manual cancellations, handle genuine connectivity errors.
                if (xhr.statusText !== 'abort') {
                    ui.lock(key, 'Bağlantı hatası oluştu!');
                }

            }

        });

    };



    /**
     * Hierarchical Trigger - Module Change:
     * Cascades updates down to the Type, Route, and Model dropdowns.
     */
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



    /**
     * Hierarchical Trigger - Type Change:
     * Refreshes the Route list and resets the dependent Model dropdown.
     */

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



    /**
     * Hierarchical Trigger - Route Change:
     * Conditional fetch for Model data, triggered only if the current type is defined as 'model'.
     */

    $(document).on('change', selectors.route, function () {
        const route = $(this).val();
        const type = $(selectors.type).val();
        const module = $(selectors.module).val();
        // Model data fetching is conditionally handled based on the route type.
        if (!route || type !== 'model') {
            ui.lock('model', messages.model.default);
            return;
        }
        fetchData('model', '/menu/item/model', { module, type, route }, (data) => ui.fill('model', data));

    });

});