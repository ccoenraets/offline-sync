window.Employee = Backbone.Model.extend({

    urlRoot:"../api/employees",

    defaults: {
        id: null,
        firstName: "",
        lastName: "",
        title: "",
        officePhone: "",
        lastModified: ""
    }


});

window.EmployeeCollection = Backbone.Collection.extend({

    model: Employee,

    url:"../api/employees"

});