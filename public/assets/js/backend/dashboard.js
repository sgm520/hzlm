define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
          $(".btn-withdrawal").click(function () {
              Fast.api.open('dashboard/withdrawal','申请提现',{
                  name:'withdrawal'
              })
          })
        },
        withdrawal: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };

    return Controller;
});
