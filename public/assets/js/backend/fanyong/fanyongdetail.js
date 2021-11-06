define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'upload'], function ($, undefined, Backend, Table, Form, Upload) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                search: true,
                advancedSearch: true,
                pagination: true,
                extend: {
                    "index_url": "fanyong/fanyongdetail/index",
                    "table": 'user_balance_log'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'user_balance_log.id',
                columns: [
                    [
                        {field: 'id', title: 'ID'},
                        {field: 'user_id', title: __('代理ID')},
                        {field: 'tel', title: __('代理手机'),operate:false},
                        {field: 'k_tel', title: __('管理员手机'),operate: 'LIKE'},
                        {field: 'change', title: __('交易金额')},
                        {field: 'fanyong.logo', title: __('logo'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'remark', title: __('产品名称'),operate: 'LIKE'},
                        {field: 'description', title: __('返佣类型')},
                        {field: 'create_time', title: __('返佣时间'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
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
