define(['jquery', 'bootstrap', 'backend', 'csmtable', 'form','fixedcolumns','tablestickyheader','tablereorderrows','tabletreegrid','xeditable2'],
    function ($, undefined, Backend, Table, Form,fixedcolumns,tablestickyheader,tablereorderrows,tabletreegrid,xeditable2) {
    var Controller = {
        index: function () {
            require(['table'], function (Table) {

                // 初始化表格参数配置
                Table.api.init({
                    extend: {
                        index_url: 'order/index',
                    }
                });
                var urlArr = [];
                var multiple = Fast.api.query('multiple');
                multiple = multiple == 'true' ? true : false;

                var table = $("#table");



                // 初始化表格
                table.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url,
                    sortName: 'id',
                    showToggle: false,
                    showExport: false,
                    columns: [
                        [
                            {field: 'name', title: __('名称'), formatter: Table.api.formatter.search, operate: 'like'},


                        ]
                    ]
                });

                // 选中多个
                $(document).on("click", ".btn-choose-multi", function () {
                    Fast.api.close({url: urlArr.join(","), multiple: multiple});
                });

                // 为表格绑定事件
                Table.api.bindevent(table);
                require(['upload'], function (Upload) {
                    Upload.api.upload($("#toolbar .faupload"), function () {
                        $(".btn-refresh").trigger("click");
                    });
                });

            });
        },
        order: function () {
            require(['table'], function (Table) {
                Table.api.init({
                    search: false,
                    advancedSearch: false,
                    pagination: true,
                    extend: {
                        "index_url": "order/order",
                    }
                });
                var table = $("#table1");
                var colums=  [
                    {field: 'configjson', title: __('数据'),operate: 'LIKE',visible: false},
                    {field: 'fanyong.name', title: __('产品名字'), operate: 'LIKE',align: 'left' },
                    {field: 'fanyong.logo', title: __('产品logo'),align: 'left' ,operate:false,events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                    {field: 'json.tu1', title: __('示例图1'),operate:false,events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                    {field: 'json.tu2', title: __('示例图2'),operate:false,events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                    {field: 'json.tu3', title: __('示例图3'),operate:false,events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                    {field: 'time', title: __('申请时间'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                    {field: 'operate', title: __('Operate'), table: table,width:150,
                        events: Table.api.events.operate,
                        buttons: [
                            {
                                name: 'agree',
                                text: __('同意'),
                                icon: 'fa fa-check',
                                classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                url: 'fanyong/fanyongorder/agree',
                                confirm: '你确定要同意吗?',
                                success:function(){
                                    table.bootstrapTable('refresh', {});
                                    return true;
                                },
                                visible:function (data) {
                                    if(data.status ==1 &&  Config.adminId ==1){
                                        return  true
                                    }else{
                                        return  false
                                    }
                                }
                            },
                            {
                                name: 'refuse',
                                text: __('拒绝'),
                                title: __('拒绝'),
                                classname: 'btn btn-xs btn-danger btn-magic btn-ajax',
                                icon: 'fa fa-close',
                                url: 'fanyong/fanyongorder/refuse',
                                confirm: '你确定要拒绝?',
                                success:function(){
                                    table.bootstrapTable('refresh', {});
                                    return true;
                                },
                                visible:function (data) {
                                    if(data.status ==1 &&  Config.adminId ==1){
                                        return  true
                                    }else{
                                        return  false
                                    }
                                }
                            },],
                        formatter: Table.api.formatter.operate
                    }
                ]
                const columnsObject=Config.column;
                for(let key  in columnsObject){
                    colums.push({
                        field:'configjson.'+key,
                        title:columnsObject[key],
                        operate:false
                    })
                }
                // 初始化表格
                table.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url,
                    pk: 'id',
                    sortName: 'time',
                    columns:[colums] ,

                });
                Table.api.bindevent(table);//当内容渲染完成后
                Form.api.bindevent($("#update-form"), function () {});


            });



        },
    };
    return Controller;
});
