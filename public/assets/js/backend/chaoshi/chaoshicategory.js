define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'upload'], function ($, undefined, Backend, Table, Form, Upload) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                search: true,
                advancedSearch: true,
                pagination: true,
                extend: {
                    "index_url": "chaoshi/chaoshicategory/index",
                    "add_url": "chaoshi/chaoshicategory/add",
                    "edit_url": "chaoshi/chaoshicategory/edit",
                    "del_url": "chaoshi/chaoshicategory/del",
                    "multi_url": "chaoshi/chaoshicategory/multi",
                    "table": 'chaoshi_category'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'chaoshi_category.list_order',
                columns: [
                    [
                        {field: 'id', title: 'ID'},
                        {field: 'name', title: __('系列名称')},
                        {field: 'category', title: __('组别分类'),searchList: {1:'网贷合集',2:'系列大全'},visible:false},
                        {field: 'categorystr', title: __('组别分类'),searchList: {1:'网贷合集',2:'系列大全'},operate:false},
                        {field: 'logo', title: __('logo'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'state', title: __('Status'), formatter: Table.api.formatter.toggle,searchList: {1:'上架中',2:'已下架'}},
                        {field: 'list_order', title: __('排序')},
                        {field: 'update_time', title: __('编辑时间'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'create_time', title: __('创建时间'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
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
