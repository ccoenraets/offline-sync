window.EmployeeView = Backbone.View.extend({

    initialize: function () {
        this.model.on("change", this.render, this);
        this.render();
    },

    events: {
        "click .save":      "save",
        "click .delete":    "destroy"
    },

    render: function () {
        $(this.el).html(this.template(this.model.toJSON()));
        return this;
    },

    save: function () {
        this.model.set({firstName: $('#firstName').val(), lastName: $('#lastName').val(), title: $('#title').val()});
        if (this.model.isNew()) {
            app.employees.create(this.model, {
                success: function (model) {
                    app.navigate('employees/' + model.id, false);
                },
                error: function(model, response) {
                    alert(response.responseText);
                }
            });
        } else {
            this.model.save();
        }
        return false;
    },

    destroy: function () {
        this.model.destroy({
            success: function () {
                alert('Employee deleted successfully');
                window.history.back();
            }
        });
        return false;
    }

});