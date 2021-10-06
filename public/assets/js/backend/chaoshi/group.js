define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'upload'], function ($, undefined, Backend, Table, Form, Upload) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                search: true,
                advancedSearch: true,
                pagination: true,
                extend: {
                    "index_url": "chaoshi/group/index",
                    "add_url": "chaoshi/group/add",
                    "edit_url": "chaoshi/group/edit",
                    "multi_url": "chaoshi/group/multi",
                    "table": 'caoshi_group'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'order_list',
                columns: [
                    [
                        {field: 'id', title: 'ID'},
                        {field: 'name', title: __('名称')},
                        {field: 'top', title: __('是否置顶'),formatter: Table.api.formatter.toggle,operate: false},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.toggle,searchList: {1:'正常',0:'已下架'}},
                        {field: 'order_list', title: __('排序'),operate: false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                commonSearch: true
            });

            // 为表格绑定事件
            Table.api.bindevent(table);//当内容渲染完成后


            // 给表单绑定事件
            Form.api.bindevent($("#update-form"), function () {

            });
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                $(document).on('click', "input[name='row[status]']", function () {
                    var name = $("input[name='row[status]']");
                    console.log(name)
                    name.prop("placeholder", $(this).val() == 1 ? name.data("placeholder-menu") : name.data("placeholder-node"));
                });
                $("input[name='row[status]']:checked").trigger("click");
                Form.api.bindevent($("form[role=form]"));
            },
            formatter:{
                status: function (value, row, index) {
                    return '<a href="' + row.fullurl + '" target="_blank" class="label bg-green">' + row.url + '</a>';
                }
            }
        }
    };
    return Controller;
});
