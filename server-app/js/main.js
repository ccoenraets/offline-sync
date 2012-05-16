var AppRouter = Backbone.Router.extend({

    routes: {
        "employees/add"         : "addEmployee",
        "employees/:id"         : "editEmployee"
    },

    initialize: function(options) {
        this.employees = options.employees;
    },

    editEmployee: function (id) {
        var employee = this.employees.get(id);
        if (this.currentView) {
            this.currentView.undelegateEvents();
            $(this.currentView.el).empty();
        }
        this.currentView = new EmployeeView({model: employee, el: "#content"});
    },

	addEmployee: function() {
        var employee = new Employee();
        if (this.currentView) {
            this.currentView.undelegateEvents();
            $(this.currentView.el).empty();
        }
        this.currentView = new EmployeeView({model: employee, el: "#content"});
	}

});

utils.loadTemplate(['HeaderView', 'EmployeeView', 'EmployeeListItemView'], function() {
    var headerView = new HeaderView({el: '.header'});
    var employees = new EmployeeCollection();
    employees.fetch({success: function(){
        var listView = new EmployeeListView({model: employees, el: "#list"});
        this.app = new AppRouter({employees: employees});
        Backbone.history.start();
    }});
});