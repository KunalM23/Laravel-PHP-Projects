(function ($) {
    // ------------------------------
    // Shared helper functions
    // ------------------------------
    // Step 1: Build API URLs in one place.
    function apiUrl(path) {
        return (window.apiBaseUrl || "/api") + path;
    }

    // Step 2: Build web URLs in one place.
    function webUrl(path) {
        return (window.location.origin || "") + path;
    }

    // Step 3: Normalize paginated and non-paginated API responses.
    // Why this is needed: some endpoints return arrays, others return { data: [] }.
    function normalizeListResponse(response) {
        if (Array.isArray(response)) {
            return { rows: response, meta: null };
        }
        return { rows: response.data || [], meta: response };
    }

    // Step 4: Read query string filters.
    function getQueryParam(name, defaultValue) {
        const val = new URLSearchParams(window.location.search).get(name);
        return val === null || val === "" ? defaultValue : val;
    }

    function getJson(url, cb) {
        $.ajax({ url: url, method: "GET", success: cb });
    }

    function showError(xhr) {
        const msg = xhr?.responseJSON?.message || "Request failed";
        alert(msg);
    }

    // ------------------------------
    // Navbar dynamic session user
    // ------------------------------
    function bindNavbarUser() {
        getJson(webUrl("/session-user"), function (user) {
            if (!user) return;

            const fallback = webUrl("/images/icon/avatar-01.jpg");
            const img = user.image ? webUrl("/storage/" + user.image) : fallback;

            $("#navbar-user-name").text(user.name || "User");
            $("#navbar-user-name-dropdown").text(user.name || "User");
            $("#navbar-user-email").text(user.email || "-");
            $("#navbar-user-image").attr("src", img);
            $("#navbar-user-image-dropdown").attr("src", img);
        });

        $("#navbar-logout-btn").on("click", function (e) {
            e.preventDefault();
            $.ajax({ url: webUrl("/session-logout"), method: "POST" })
                .done(function () { window.location.href = webUrl("/auth/admin-login"); })
                .fail(showError);
        });
    }

    // ------------------------------
    // Login pages (simple session)
    // ------------------------------
    function bindSessionLogin() {
        $(".login-form form").on("submit", function (e) {
            e.preventDefault();
            const email = $(this).find("input[name='email']").val();
            $.ajax({ url: webUrl("/session-login"), method: "POST", data: { email: email } })
                .done(function () { window.location.href = webUrl("/dashboard"); })
                .fail(showError);
        });
    }

    function buildListQuery() {
        const search = getQueryParam("search", "");
        const status = getQueryParam("status", "");
        const fromDate = getQueryParam("from_date", "");
        const toDate = getQueryParam("to_date", "");
        const sortBy = getQueryParam("sort_by", "id");
        const sortDir = getQueryParam("sort_dir", "desc");
        const page = getQueryParam("page", "1");

        return $.param({ search: search, status: status, from_date: fromDate, to_date: toDate, sort_by: sortBy, sort_dir: sortDir, page: page, per_page: 10 });
    }

    function usersAdd() {
        // Step 1: Handle image upload + user fields.
        const $form = $(".main-content form.form-horizontal").first();
        $form.on("submit", function (e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append("name", $("#email-input[placeholder='Enter Name']").val() || "");
            formData.append("email", $("#email-input[placeholder='Enter Email']").val() || "");
            formData.append("username", $("#email-input[placeholder='Enter Username']").val() || "");
            formData.append("password", $("#email-input[placeholder='Enter Password']").val() || "");
            formData.append("gender", $("input[name='inline-radios']:checked").parent().text().trim().toLowerCase() || "");
            formData.append("status", ($("#select").first().val() === "1" ? "active" : "inactive"));
            formData.append("designation_id", $("#select").eq(1).val() || "");
            formData.append("remarks", $("#textarea-input").val() || "");

            const imageInput = $("#file-input")[0];
            if (imageInput && imageInput.files.length) {
                formData.append("image", imageInput.files[0]);
            }

            $.ajax({
                url: apiUrl("/users"),
                method: "POST",
                data: formData,
                processData: false,
                contentType: false
            })
                .done(function () { alert("User created successfully."); $form[0].reset(); })
                .fail(showError);
        });
    }

    function usersList() {
        const $tbody = $(".table-data2 tbody");
        function load() {
            getJson(apiUrl("/users?" + buildListQuery()), function (response) {
                const parsed = normalizeListResponse(response);
                const users = parsed.rows;
                $tbody.empty();
                users.forEach(function (u, i) {
                    const image = u.image ? ("<img src='/storage/" + u.image + "' alt='user' style='height:32px;width:32px;border-radius:50%;margin-right:8px;'>") : "";
                    $tbody.append(
                        "<tr class='tr-shadow'>" +
                        "<td>" + (i + 1) + ".</td>" +
                        "<td>" + (u.id || "") + "</td>" +
                        "<td>" + image + (u.name || "") + "</td>" +
                        "<td><span class='block-email'>" + (u.email || "") + "</span></td>" +
                        "<td><span class='role user'>" + (u.role || "user") + "</span></td>" +
                        "<td class='desc'>" + (u.designation_id || "") + "</td>" +
                        "<td><span class='status--process'>" + ((u.status || "active")) + "</span></td>" +
                        "<td><div class='table-data-feature'><button class='item js-edit' data-id='" + u.id + "' title='Edit'><i class='zmdi zmdi-edit edit'></i></button><button class='item js-delete' data-id='" + u.id + "' title='Delete'><i class='zmdi zmdi-delete delete'></i></button></div></td>" +
                        "</tr><tr class='spacer'></tr>"
                    );
                });
            });
        }
        $tbody.on("click", ".js-delete", function () {
            $.ajax({ url: apiUrl("/users/" + $(this).data("id")), method: "DELETE" }).done(load).fail(showError);
        });
        $tbody.on("click", ".js-edit", function () {
            const id = $(this).data("id");
            const current = $(this).closest("tr").find("td").eq(2).text().trim();
            const newName = prompt("Update user name", current);
            if (!newName) return;
            $.ajax({ url: apiUrl("/users/" + id), method: "PUT", data: { name: newName } }).done(load).fail(showError);
        });
        load();
    }

    function usersRoles() {
        const $tbody = $(".table tbody");
        getJson(apiUrl("/users"), function (users) {
            $tbody.empty();
            users.forEach(function (u) {
                $tbody.append(
                    "<tr><td><div class='table-data__info'><h6>" + (u.name || "") + "</h6><span><a href='#'>" + (u.email || "") + "</a></span></div></td>" +
                    "<td><span class='role user'>" + (u.role || "user") + "</span></td>" +
                    "<td><div class='rs-select2--trans rs-select2--sm'><select class='js-select2 role-select'><option value='active'" + ((u.status === "active" || !u.status) ? " selected" : "") + ">Full Control</option><option value='inactive'" + (u.status === "inactive" ? " selected" : "") + ">Watch</option></select><div class='dropDownSelect2'></div></div></td>" +
                    "<td><button type='button' data-id='" + u.id + "' class='btn btn-warning btn-sm js-role-update'>Update</button></td></tr>"
                );
            });
        });
        $tbody.on("click", ".js-role-update", function () {
            const id = $(this).data("id");
            const status = $(this).closest("tr").find(".role-select").val();
            $.ajax({ url: apiUrl("/users/" + id), method: "PUT", data: { status: status } })
                .done(function () { alert("Role settings updated."); })
                .fail(showError);
        });
    }

    function leadsAdd() {
        const $form = $(".main-content form.form-horizontal").first();
        $form.on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: apiUrl("/leads"),
                method: "POST",
                data: {
                    name: $("#leads-name-input").val(),
                    email: $("#leads-email-input").val(),
                    phone: $("#leads-phone-input").val(),
                    company: $("#leads-company-input").val(),
                    source_id: $("#leads-source").val(),
                    status_id: $("#leads-status").val(),
                    assigned_to: $("#leads-assigned-to").val()
                }
            }).done(function () { alert("Lead created successfully."); $form[0].reset(); }).fail(showError);
        });
    }

    function leadsList() {
        const $tbody = $(".table-data2 tbody");
        function load() {
            getJson(apiUrl("/leads?" + buildListQuery()), function (response) {
                const rows = normalizeListResponse(response).rows;
                $tbody.empty();
                rows.forEach(function (r, i) {
                    $tbody.append("<tr class='tr-shadow'><td>" + (i + 1) + "</td><td>" + (r.name || "") + "</td><td>" + (r.email || "") + "</td><td>" + (r.phone || "") + "</td><td>" + (r.company || "") + "</td><td><div class='table-data-feature'><button class='item js-edit-lead' data-id='" + r.id + "'><i class='zmdi zmdi-edit edit'></i></button><button class='item js-delete-lead' data-id='" + r.id + "'><i class='zmdi zmdi-delete delete'></i></button></div></td></tr><tr class='spacer'></tr>");
                });
            });
        }
        $tbody.on("click", ".js-edit-lead", function () {
            const id = $(this).data("id");
            const current = $(this).closest("tr").find("td").eq(1).text().trim();
            const name = prompt("Update lead name", current);
            if (!name) return;
            $.ajax({ url: apiUrl("/leads/" + id), method: "PUT", data: { name: name } }).done(load).fail(showError);
        });
        $tbody.on("click", ".js-delete-lead", function () {
            $.ajax({ url: apiUrl("/leads/" + $(this).data("id")), method: "DELETE" }).done(load).fail(showError);
        });
        load();
    }

    function interactionsAdd() {
        const $form = $(".main-content form.form-horizontal").first();
        $form.on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: apiUrl("/interactions"),
                method: "POST",
                data: {
                    lead_id: $("#interaction-lead-name").val(),
                    user_id: 1,
                    interaction_type_id: $("#interaction-lead-type").val(),
                    interaction_date: $("#interaction-lead-date").val(),
                    notes: $("#interaction-lead-note").val()
                }
            }).done(function () { alert("Interaction created successfully."); $form[0].reset(); }).fail(showError);
        });
    }

    function interactionsList() {
        const $tbody = $(".table-data2 tbody");
        function load() {
            getJson(apiUrl("/interactions?" + buildListQuery()), function (response) {
                const rows = normalizeListResponse(response).rows;
                $tbody.empty();
                rows.forEach(function (r, i) {
                    $tbody.append("<tr class='tr-shadow'><td>" + (i + 1) + "</td><td>" + (r.lead_id || "") + "</td><td>" + (r.user_id || "") + "</td><td>" + (r.interaction_type_id || "") + "</td><td>" + (r.interaction_date || "") + "</td><td><div class='table-data-feature'><button class='item js-edit-interaction' data-id='" + r.id + "'><i class='zmdi zmdi-edit edit'></i></button><button class='item js-delete-interaction' data-id='" + r.id + "'><i class='zmdi zmdi-delete delete'></i></button></div></td></tr><tr class='spacer'></tr>");
                });
            });
        }
        $tbody.on("click", ".js-edit-interaction", function () {
            const id = $(this).data("id");
            const notes = prompt("Update notes");
            if (notes === null) return;
            $.ajax({ url: apiUrl("/interactions/" + id), method: "PUT", data: { notes: notes } }).done(load).fail(showError);
        });
        $tbody.on("click", ".js-delete-interaction", function () {
            $.ajax({ url: apiUrl("/interactions/" + $(this).data("id")), method: "DELETE" }).done(load).fail(showError);
        });
        load();
    }

    function tasksAdd() {
        const $form = $(".main-content form.form-horizontal").first();
        $form.on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: apiUrl("/tasks"),
                method: "POST",
                data: {
                    title: $("#task-title-input").val(),
                    lead_id: $("#task-lead-select").val(),
                    user_id: $("#task-assign-select").val(),
                    status_id: $("#task-status-select").val(),
                    priority: $("#task-priority-select").val(),
                    due_date: $("#task-duedate-select").val(),
                    description: $("#textarea-input").val()
                }
            }).done(function () { alert("Task created successfully."); $form[0].reset(); }).fail(showError);
        });
    }

    function tasksList() {
        const $tbody = $(".table-data2 tbody");
        function load() {
            getJson(apiUrl("/tasks?" + buildListQuery()), function (response) {
                const rows = normalizeListResponse(response).rows;
                $tbody.empty();
                rows.forEach(function (r, i) {
                    $tbody.append("<tr class='tr-shadow'><td>" + (i + 1) + "</td><td>" + (r.title || "") + "</td><td>" + (r.lead_id || "") + "</td><td>" + (r.user_id || "") + "</td><td>" + (r.status_id || "") + "</td><td>" + (r.priority || "") + "</td><td><div class='table-data-feature'><button class='item js-edit-task' data-id='" + r.id + "'><i class='zmdi zmdi-edit edit'></i></button><button class='item js-delete-task' data-id='" + r.id + "'><i class='zmdi zmdi-delete delete'></i></button></div></td></tr><tr class='spacer'></tr>");
                });
            });
        }
        $tbody.on("click", ".js-edit-task", function () {
            const id = $(this).data("id");
            const current = $(this).closest("tr").find("td").eq(1).text().trim();
            const title = prompt("Update task title", current);
            if (!title) return;
            $.ajax({ url: apiUrl("/tasks/" + id), method: "PUT", data: { title: title } }).done(load).fail(showError);
        });
        $tbody.on("click", ".js-delete-task", function () {
            $.ajax({ url: apiUrl("/tasks/" + $(this).data("id")), method: "DELETE" }).done(load).fail(showError);
        });
        load();
    }

    // ------------------------------
    // Dashboard charts and reports
    // ------------------------------
    function dashboardInit() {
        getJson(webUrl("/dashboard-data"), function (data) {
            // Admin cards (keep same template classes, only update values).
            $(".overview-item--c1 .text h2").first().text(data.user.my_pending || 0);
            $(".overview-item--c2 .text h2").first().text(data.admin.tasks_count || 0);
            $(".overview-item--c3 .text h2").first().text(data.admin.leads_count || 0);

            // If Chart.js canvas exists in template, draw simple charts.
            if (typeof Chart !== "undefined" && $("#widgetChart1").length) {
                const labels = (data.admin.task_status_breakdown || []).map(function (x) { return "Status " + x.status_id; });
                const values = (data.admin.task_status_breakdown || []).map(function (x) { return x.total; });
                new Chart($("#widgetChart1"), {
                    type: "doughnut",
                    data: { labels: labels, datasets: [{ data: values, backgroundColor: ["#00b5e9", "#fa4251", "#00ad5f", "#f1c40f"] }] }
                });
            }
        });
    }

    // ------------------------------
    // Export helpers
    // ------------------------------
    function bindExports() {
        // Step 1: Reuse existing "Export" dropdown/button areas.
        // Simpler alternative: add separate export buttons.
        $(".table-data__tool-right").on("dblclick", function () {
            const page = window.crmPage || "";
            if (page === "users.list") window.open(apiUrl("/users-export-excel"), "_blank");
            if (page === "leads.list") window.open(apiUrl("/leads-export-excel"), "_blank");
            if (page === "interactions.list") window.open(apiUrl("/interactions-export-excel"), "_blank");
            if (page === "tasks.list") window.open(apiUrl("/tasks-export-excel"), "_blank");
        });
    }

    $(function () {
        bindNavbarUser();
        bindExports();

        if (window.location.pathname.indexOf("/auth/") === 0) {
            bindSessionLogin();
        }

        switch (window.crmPage) {
            case "dashboard": dashboardInit(); break;
            case "users.add": usersAdd(); break;
            case "users.list": usersList(); break;
            case "users.roles": usersRoles(); break;
            case "leads.add": leadsAdd(); break;
            case "leads.list": leadsList(); break;
            case "interactions.add": interactionsAdd(); break;
            case "interactions.list": interactionsList(); break;
            case "tasks.add": tasksAdd(); break;
            case "tasks.list": tasksList(); break;
            default: break;
        }
    });
})(jQuery);
