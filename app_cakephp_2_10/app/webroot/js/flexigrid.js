/*
 * Flexigrid for jQuery -  v1.1
 *
 * Copyright (c) 2008 Paulo P. Marinas (code.google.com/p/flexigrid/)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 */
(function ($) {
	$.addFlex = function (t, p) {
		if (t.grid) return false; //return if already exist
		p = $.extend({ //apply default properties
            checkboxSelection: false, //apply checkbox selection
            in_edit_mode:false,
            current_edit_tr : false,
            reload_after_add : false,
            reload_after_edit : false,
            callback:null,
            before_collapse:null,
            after_collapse:null,
            before_expand:null,
            after_expand:null,
            editable:$.extend({
                use:false,
                add:true,
                edit:true,
                url:false,
                dataType:'json',
                method:'POST',
                beforeSave:null,
                beforeRender:null,
                afterRender:null,
                confirmSave:false,
                confirmSaveText:"Are you sure you want to proceed ?",
                callback:null
            }, p.editablegrid),
            subGrid:$.extend({
                use:false,
                add:true,
                edit:true,
                url:false,
                dataType:'json',
                method:'POST',
                width:'100',
                max_height:false,
                editable:$.extend({
                    use:false,
                    url:false,
                    dataType:'json',
                    method:'POST',
                    beforeSave:null,
                    beforeRender:null,
                    afterRender:null,
                    confirmSave:false,
                    confirmSaveText:"Are you sure you want to proceed ?",
                    callback:null
                }, p.subgrid.editablegrid)
            }, p.subgrid), //prepare for sub grid
            subgrid_expanded_binded:false,
            columnControl: false, // apply column control
			height: 200, //default height
			width: 'auto', //auto width
			striped: true, //apply odd even stripes
			novstripe: false,
			minwidth: 30, //min width of columns
			minheight: 80, //min height of columns
			resizable: true, //allow table resizing
			url: false, //URL if using data from AJAX
			method: 'POST', //data sending method
			dataType: 'xml', //type of data for AJAX, either xml or json
			errormsg: 'Connection Error',
			usepager: false,
			nowrap: true,
			page: 1, //current page
			total: 1, //total pages
			useRp: true, //use the results per page select box
			rp: 15, //results per page
			rpOptions: [10, 15, 20, 30, 50, 100], //allowed per-page values 
			title: false,
			pagestat: 'Displaying {from} to {to} of {total} items',
			pagetext: 'Page',
			outof: 'of',
			findtext: 'Find',
			procmsg: 'Processing, please wait ...',
			query: '',
			qtype: '',
			nomsg: 'No items',
			minColToggle: 1, //minimum allowed column to be hidden
			showToggleBtn: true, //show or hide column toggle popup
			hideOnSubmit: true,
			autoload: true,
			blockOpacity: 0.5,
			preProcess: false,
			onDragCol: false,
			onToggleCol: false,
			onChangeSort: false,
			onSuccess: false,
			onError: false,
			onSubmit: false //using a custom populate function
		}, p);
		$(t).show() //show if hidden
			.attr({
				cellPadding: 0,
				cellSpacing: 0,
				border: 0
			}) //remove padding and spacing
			.removeAttr('width'); //remove width properties
		//create grid class
		var g = {
			hset: {},
			rePosDrag: function () {
				var cdleft = 0 - this.hDiv.scrollLeft;
				if (this.hDiv.scrollLeft > 0) cdleft -= Math.floor(p.cgwidth / 2);
				$(g.cDrag).css({
					top: g.hDiv.offsetTop + 1
				});
				var cdpad = this.cdpad;
				$('div', g.cDrag).hide();
				$('thead tr:first th:visible', this.hDiv).each(function () {
					var n = $('thead tr:first th:visible', g.hDiv).index(this);
					var cdpos = parseInt($('div', this).width());
					if (cdleft == 0) cdleft -= Math.floor(p.cgwidth / 2);
					cdpos = cdpos + cdleft + cdpad;
					if (isNaN(cdpos)) {
						cdpos = 0;
					}
					$('div:eq(' + n + ')', g.cDrag).css({
						'left': cdpos + 'px'
					}).show();
					cdleft = cdpos;
				});
			},
			fixHeight: function (newH) {
				newH = false;
				if (!newH) newH = $(g.bDiv).height();
				var hdHeight = $(this.hDiv).height();
				$('div', this.cDrag).each(
					function () {
						$(this).height(newH + hdHeight);
					}
				);
				var nd = parseInt($(g.nDiv).height());
				if (nd > newH) $(g.nDiv).height(newH).width(200);
				else $(g.nDiv).height('auto').width('auto');
				$(g.block).css({
					height: newH,
					marginBottom: (newH * -1)
				});
				var hrH = g.bDiv.offsetTop + newH;
				if (p.height != 'auto' && p.resizable) hrH = g.vDiv.offsetTop;
				$(g.rDiv).css({
					height: hrH
				});
			},
			dragStart: function (dragtype, e, obj) { //default drag function start
				if (dragtype == 'colresize') {//column resize
					$(g.nDiv).hide();
					$(g.nBtn).hide();
					var n = $('div', this.cDrag).index(obj);
					var ow = $('th:visible div:eq(' + n + ')', this.hDiv).width();
					$(obj).addClass('dragging').siblings().hide();
					$(obj).prev().addClass('dragging').show();
					this.colresize = {
						startX: e.pageX,
						ol: parseInt(obj.style.left),
						ow: ow,
						n: n
					};
					$('body').css('cursor', 'col-resize');
				} else if (dragtype == 'vresize') {//table resize
					var hgo = false;
					$('body').css('cursor', 'row-resize');
					if (obj) {
						hgo = true;
						$('body').css('cursor', 'col-resize');
					}
					this.vresize = {
						h: p.height,
						sy: e.pageY,
						w: p.width,
						sx: e.pageX,
						hgo: hgo
					};
				} else if (dragtype == 'colMove') {//column header drag
					$(g.nDiv).hide();
					$(g.nBtn).hide();
					this.hset = $(this.hDiv).offset();
					this.hset.right = this.hset.left + $('table', this.hDiv).width();
					this.hset.bottom = this.hset.top + $('table', this.hDiv).height();
					this.dcol = obj;
					this.dcoln = $('th', this.hDiv).index(obj);
					this.colCopy = document.createElement("div");
					this.colCopy.className = "colCopy";
					this.colCopy.innerHTML = obj.innerHTML;
					if ($.browser.msie) {
						this.colCopy.className = "colCopy ie";
					}
					$(this.colCopy).css({
						position: 'absolute',
						float: 'left',
						display: 'none',
						textAlign: obj.align
					});
					$('body').append(this.colCopy);
					$(this.cDrag).hide();
				}
				$('body').noSelect();
			},
			dragMove: function (e) {
				if (this.colresize) {//column resize
					var n = this.colresize.n;
					var diff = e.pageX - this.colresize.startX;
					var nleft = this.colresize.ol + diff;
					var nw = this.colresize.ow + diff;
					if (nw > p.minwidth) {
						$('div:eq(' + n + ')', this.cDrag).css('left', nleft);
						this.colresize.nw = nw;
					}
				} else if (this.vresize) {//table resize
					var v = this.vresize;
					var y = e.pageY;
					var diff = y - v.sy;
					if (!p.defwidth) p.defwidth = p.width;
					if (p.width != 'auto' && !p.nohresize && v.hgo) {
						var x = e.pageX;
						var xdiff = x - v.sx;
						var newW = v.w + xdiff;
						if (newW > p.defwidth) {
							this.gDiv.style.width = newW + 'px';
							p.width = newW;
						}
					}
					var newH = v.h + diff;
					if ((newH > p.minheight || p.height < p.minheight) && !v.hgo) {
						this.bDiv.style.height = newH + 'px';
						p.height = newH;
						this.fixHeight(newH);
					}
					v = null;
				} else if (this.colCopy) {
					$(this.dcol).addClass('thMove').removeClass('thOver');
					if (e.pageX > this.hset.right || e.pageX < this.hset.left || e.pageY > this.hset.bottom || e.pageY < this.hset.top) {
						//this.dragEnd();
						$('body').css('cursor', 'move');
					} else {
						$('body').css('cursor', 'pointer');
					}
					$(this.colCopy).css({
						top: e.pageY + 10,
						left: e.pageX + 20,
						display: 'block'
					});
				}
			},
			dragEnd: function () {
				if (this.colresize) {
					var n = this.colresize.n;
					var nw = this.colresize.nw;
					$('th:visible div:eq(' + n + ')', this.hDiv).css('width', nw);
					$('tr', this.bDiv).each(
						function () {
							$('td:visible div:eq(' + n + ')', this).css('width', nw);
						}
					);
					this.hDiv.scrollLeft = this.bDiv.scrollLeft;
					$('div:eq(' + n + ')', this.cDrag).siblings().show();
					$('.dragging', this.cDrag).removeClass('dragging');
					this.rePosDrag();
					this.fixHeight();
					this.colresize = false;
				} else if (this.vresize) {
					this.vresize = false;
				} else if (this.colCopy) {
					$(this.colCopy).remove();
					if (this.dcolt != null) {
						if (this.dcoln > this.dcolt) $('th:eq(' + this.dcolt + ')', this.hDiv).before(this.dcol);
						else $('th:eq(' + this.dcolt + ')', this.hDiv).after(this.dcol);
						this.switchCol(this.dcoln, this.dcolt);
						$(this.cdropleft).remove();
						$(this.cdropright).remove();
						this.rePosDrag();
						if (p.onDragCol) {
							p.onDragCol(this.dcoln, this.dcolt);
						}
					}
					this.dcol = null;
					this.hset = null;
					this.dcoln = null;
					this.dcolt = null;
					this.colCopy = null;
					$('.thMove', this.hDiv).removeClass('thMove');
					$(this.cDrag).show();
				}
				$('body').css('cursor', 'default');
				$('body').noSelect(false);
			},
			toggleCol: function (cid, visible) {
				var ncol = $("th[axis='col" + cid + "']", this.hDiv)[0];
				var n = $('thead th', g.hDiv).index(ncol);
				var cb = $('input[value=' + cid + ']', g.nDiv)[0];
				if (visible == null) {
					visible = ncol.hidden;
				}
				if ($('input:checked', g.nDiv).length < p.minColToggle && !visible) {
					return false;
				}
				if (visible) {
					ncol.hidden = false;
					$(ncol).show();
					cb.checked = true;
				} else {
					ncol.hidden = true;
					$(ncol).hide();
					cb.checked = false;
				}
				$('tbody tr', t).each(
					function () {
						if (visible) {
							$('td:eq(' + n + ')', this).show();
						} else {
							$('td:eq(' + n + ')', this).hide();
						}
					}
				);
				this.rePosDrag();
				if (p.onToggleCol) {
					p.onToggleCol(cid, visible);
				}
				return visible;
			},
			switchCol: function (cdrag, cdrop) { //switch columns
				$('tbody tr', t).each(
					function () {
						if (cdrag > cdrop) $('td:eq(' + cdrop + ')', this).before($('td:eq(' + cdrag + ')', this));
						else $('td:eq(' + cdrop + ')', this).after($('td:eq(' + cdrag + ')', this));
					}
				);
				//switch order in nDiv
				if (cdrag > cdrop) {
					$('tr:eq(' + cdrop + ')', this.nDiv).before($('tr:eq(' + cdrag + ')', this.nDiv));
				} else {
					$('tr:eq(' + cdrop + ')', this.nDiv).after($('tr:eq(' + cdrag + ')', this.nDiv));
				}
				if ($.browser.msie && $.browser.version < 7.0) {
					$('tr:eq(' + cdrop + ') input', this.nDiv)[0].checked = true;
				}
				this.hDiv.scrollLeft = this.bDiv.scrollLeft;
			},
			scroll: function () {
				this.hDiv.scrollLeft = this.bDiv.scrollLeft;
				this.rePosDrag();
			},
			addData: function (data) { //parse data
                //clear all editing rows
                p.in_edit_mode = false;
                p.current_edit_tr = false;

				if (p.dataType == 'json') {
					data = $.extend({rows: [], page: 0, total: 0}, data);
				}
				if (p.preProcess) {
					data = p.preProcess(data);
				}
				$('.pReload', this.pDiv).removeClass('loading');
				this.loading = false;
				if (!data) {
					$('.pPageStat', this.pDiv).html(p.errormsg);
					return false;
				}
				if (p.dataType == 'xml') {
					p.total = +$('rows total', data).text();
				} else {
					p.total = data.total;
				}
				if (p.total == 0) {
					$('tr, a, td, div', t).unbind();
					$(t).empty();
					p.pages = 1;
					p.page = 1;
					this.buildpager();
					$('.pPageStat', this.pDiv).html(p.nomsg);
					//return false; //Mek, even if there is no data to display, allow the process to continue
				}
				p.pages = Math.ceil(p.total / p.rp);
				if (p.dataType == 'xml') {
					p.page = +$('rows page', data).text();
				} else {
					p.page = data.page;
				}
				this.buildpager();
				//build new body
				var tbody = document.createElement('tbody');
                $(tbody).addClass('master');
				if (p.dataType == 'json') {
					$.each(data.rows, function (i, row) {
					    /** MEK **/
                        //Checbox selection then inject to row data
                        if(p.checkboxSelection){
                           var holder = row.cell;
                           var checkbox_sel = ["<input type='checkbox' name='checkAll' id='checkAllid"+i+"' value=''/>"];
                           row.cell = checkbox_sel.concat(holder);
                        }
                        //Subgrid menu
                        if(p.subGrid.use){
                            var holder = row.cell;
                            var subg_menu = ["<a  href='javascript:void(0);' class='expand' value='"+row.id+"' title='Click to expand' style='padding: 1px 10px'>&nbsp;</a>"];
                            row.cell = subg_menu.concat(holder);
                        }

						var tr = document.createElement('tr');
                        $(tr).attr('data-id',row.id);
                        $(tr).attr('class','hover-me master-row');

                        var extra_data_str = "";
                        //Storing extra info that may need to be passed back to the server
                        if(row.extra_data){
                            for(var c in row.extra_data){
                                extra_data_str += ""+c+"=>"+row.extra_data[c]+","
                            }
                            $(tr).attr('extra-data',extra_data_str);
                        }

						if (i % 2 && p.striped) {
							//tr.className = 'erow';
                            $(tr).attr('class','hover-me master-row erow');
						}
						if (row.id) {
							tr.id = 'row' + row.id;
						}

                        //Check if this data row contain some properties, like property.bg_color a css background color class for this row , property.edit_row a yes or no value for preventing this row from being editable
                        if(row.property){
                            if(row.property.bg_color){//Set the rows background color if any
                                $(tr).addClass(row.property.bg_color);
                            }
                            if(row.property.edit_row){//Set the rows editable state that will be applied when beginEdit is invoked.
                                $(tr).attr('edit_row',row.property.edit_row);
                            }
                        }

						$('thead tr:first th', g.hDiv).each( //add cell
							function () {
								var td = document.createElement('td');
                                $(td).attr('field', $(this).attr('field'));
								var idx = $(this).attr('axis').substr(3);
                                var editable = $(this).attr('editable');
                                $(td).attr('editable', editable);
                                if(editable == 'yes'){//Cell is editable
                                    $(td).attr('form', $(this).attr('form'));
                                    $(td).attr('validate', $(this).attr('validate'));
                                    $(td).attr('default', $(this).attr('default'));
                                    if($(this).attr('bclass')){
                                        $(td).attr('bclass', $(this).attr('bclass'));
                                    }
                                }
								td.align = this.align;
                                
								// If the json elements aren't named (which is typical), use numeric order
								if (typeof row.cell[idx] != "undefined") {
									td.innerHTML = (row.cell[idx] != null) ? row.cell[idx] : '';//null-check for Opera-browser
                                    $(td).attr('data-id', (row.cell[idx] != null) ? row.cell[idx] : '');
								} else {
									td.innerHTML = row.cell[p.colModel[idx].name];
                                    $(td).attr('data-id', row.cell[p.colModel[idx].name]);
								}
								$(td).attr('abbr', $(this).attr('abbr'));
								$(tr).append(td);
								td = null;
							}
						);
						if ($('thead', this.gDiv).length < 1) {//handle if grid has no headers
							for (idx = 0; idx < cell.length; idx++) {
								var td = document.createElement('td');
								// If the json elements aren't named (which is typical), use numeric order
								if (typeof row.cell[idx] != "undefined") {
									td.innerHTML = (row.cell[idx] != null) ? row.cell[idx] : '';//null-check for Opera-browser
								} else {
									td.innerHTML = row.cell[p.colModel[idx].name];
								}
								$(tr).append(td);
								td = null;
							}
						}
						$(tbody).append(tr);
						tr = null;
					});
				} else if (p.dataType == 'xml') {
					var i = 1;
					$("rows row", data).each(function () {
						i++;
						var tr = document.createElement('tr');
						if (i % 2 && p.striped) {
							tr.className = 'erow';
						}
						var nid = $(this).attr('id');
						if (nid) {
							tr.id = 'row' + nid;
						}
						nid = null;
						var robj = this;
						$('thead tr:first th', g.hDiv).each(function () {
							var td = document.createElement('td');
							var idx = $(this).attr('axis').substr(3);
							td.align = this.align;
							td.innerHTML = $("cell:eq(" + idx + ")", robj).text();
							$(td).attr('abbr', $(this).attr('abbr'));
							$(tr).append(td);
							td = null;
						});
						if ($('thead', this.gDiv).length < 1) {//handle if grid has no headers
							$('cell', this).each(function () {
								var td = document.createElement('td');
								td.innerHTML = $(this).text();
								$(tr).append(td);
								td = null;
							});
						}
						$(tbody).append(tr);
						tr = null;
						robj = null;
					});
				}
				$('tr', t).unbind();
				$(t).empty();
				$(t).append(tbody);
				this.addCellProp();
				this.addRowProp();
                if(p.checkboxSelection){
                   this.addT_bodydCheckboxEvent();
                }
                if(p.editable.use){
                    this.bindEditng();
                }
                if(p.subGrid.use){
                    if(p.subgrid_expanded_binded){
                    //This is a live bind action we don't want to rebind live as it behaves funny
                    }
                    else{
                        this.addSubgridMenuEvent();
                        p.subgrid_expanded_binded = true
                    }
                }
				this.rePosDrag();
				tbody = null;
				data = null;
				i = null;
				if (p.onSuccess) {
					p.onSuccess(this);
				}
				if (p.hideOnSubmit) {
					$(g.block).remove();
				}
				this.hDiv.scrollLeft = this.bDiv.scrollLeft;
				if ($.browser.opera) {
					$(t).css('visibility', 'visible');
				}
			},
			changeSort: function (th) { //change sortorder
				if (this.loading) {
					return true;
				}
				$(g.nDiv).hide();
				$(g.nBtn).hide();
				if (p.sortname == $(th).attr('abbr')) {
					if (p.sortorder == 'asc') {
						p.sortorder = 'desc';
					} else {
						p.sortorder = 'asc';
					}
				}
				$(th).addClass('sorted').siblings().removeClass('sorted');
				$('.sdesc', this.hDiv).removeClass('sdesc');
				$('.sasc', this.hDiv).removeClass('sasc');
				$('div', th).addClass('s' + p.sortorder);
				p.sortname = $(th).attr('abbr');
				if (p.onChangeSort) {
					p.onChangeSort(p.sortname, p.sortorder);
				} else {
					this.populate();
				}
			},
			buildpager: function () { //rebuild pager based on new properties
				$('.pcontrol input', this.pDiv).val(p.page);
				$('.pcontrol span', this.pDiv).html(p.pages);
				var r1 = (p.page - 1) * p.rp + 1;
				var r2 = r1 + p.rp - 1;
				if (p.total < r2) {
					r2 = p.total;
				}
				var stat = p.pagestat;
				stat = stat.replace(/{from}/, r1);
				stat = stat.replace(/{to}/, r2);
				stat = stat.replace(/{total}/, p.total);
				$('.pPageStat', this.pDiv).html(stat);
			},
			populate: function () { //get latest data
                $("#flex_modal_notify a.flex-close").click();
				if (this.loading) {
					return true;
				}
				if (p.onSubmit) {
					var gh = p.onSubmit();
					if (!gh) {
						return false;
					}
				}
				this.loading = true;
				if (!p.url) {
					return false;
				}
				$('.pPageStat', this.pDiv).html(p.procmsg);
				$('.pReload', this.pDiv).addClass('loading');
				$(g.block).css({
					top: g.bDiv.offsetTop,
                    background: 'white'
				});
				if (p.hideOnSubmit) {
					$(this.gDiv).prepend(g.block);
				}
				if ($.browser.opera) {
					$(t).css('visibility', 'hidden');
				}
				if (!p.newp) {
					p.newp = 1;
				}
				if (p.page > p.pages) {
					p.page = p.pages;
				}
				var param = [{
					name: 'page',
					value: p.newp
				}, {
					name: 'rp',
					value: p.rp
				}, {
					name: 'sortname',
					value: p.sortname
				}, {
					name: 'sortorder',
					value: p.sortorder
				}, {
					name: 'query',
					value: p.query
				}, {
					name: 'qtype',
					value: p.qtype
				}];
				if (p.params) {
					for (var pi = 0; pi < p.params.length; pi++) {
						param[param.length] = p.params[pi];
					}
				}
				$.ajax({
					type: p.method,
					url: p.url,
					data: param,
					dataType: p.dataType,
					success: function (data) {
						g.addData(data);
                        //Clear Header Checkbok 
                        if(p.checkboxSelection){
                            g.toggleT_headCheckbox()
                        }
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						try {
							if (p.onError) p.onError(XMLHttpRequest, textStatus, errorThrown);
						} catch (e) {}
					}
				});
			},
			doSearch: function () {
				p.query = $('input[name=q]', g.sDiv).val();
				p.qtype = $('select[name=qtype]', g.sDiv).val();
				p.newp = 1;
				this.populate();
			},
            closeSearch: function () {
                $('input[name=q]', g.sDiv).val('');
                $('select[name=qtype]', g.sDiv).val();
                //Hide the search
                $('.pSearch', g.pDiv).click();
                $('button.flex_search_btn', g.sDiv).click();
            },
			changePage: function (ctype) { //change page
				if (this.loading) {
					return true;
				}
				switch (ctype) {
					case 'first':
						p.newp = 1;
						break;
					case 'prev':
						if (p.page > 1) {
							p.newp = parseInt(p.page) - 1;
						}
						break;
					case 'next':
						if (p.page < p.pages) {
							p.newp = parseInt(p.page) + 1;
						}
						break;
					case 'last':
						p.newp = p.pages;
						break;
					case 'input':
						var nv = parseInt($('.pcontrol input', this.pDiv).val());
						if (isNaN(nv)) {
							nv = 1;
						}
						if (nv < 1) {
							nv = 1;
						} else if (nv > p.pages) {
							nv = p.pages;
						}
						$('.pcontrol input', this.pDiv).val(nv);
						p.newp = nv;
						break;
				}
				if (p.newp == p.page) {
					return false;
				}
				if (p.onChangePage) {
					p.onChangePage(p.newp);
				} else {
					this.populate();
				}
			},
			addCellProp: function () {
				$('tbody tr td', g.bDiv).each(function () {
					var tdDiv = document.createElement('div');
					var n = $('td', $(this).parent()).index(this);
					var pth = $('th:eq(' + n + ')', g.hDiv).get(0);
					if (pth != null) {
						if (p.sortname == $(pth).attr('abbr') && p.sortname) {
							this.className = 'sorted';
						}
						$(tdDiv).css({
							textAlign: pth.align,
							width: $('div:first', pth)[0].style.width
						});
						if (pth.hidden) {
							$(this).css('display', 'none');
						}
					}
					if (p.nowrap == false) {
						$(tdDiv).css('white-space', 'normal');
					}
					if (this.innerHTML == '') {
						this.innerHTML = '&nbsp;';
					}
					tdDiv.innerHTML = this.innerHTML;
					var prnt = $(this).parent()[0];
					var pid = false;
					if (prnt.id) {
						pid = prnt.id.substr(3);
					}
					if (pth != null) {
						if (pth.process) pth.process(tdDiv, pid);
					}
					$(this).empty().append(tdDiv).removeAttr('width'); //wrap content
				});
			},
			getCellDim: function (obj) {// get cell prop for editable event
				var ht = parseInt($(obj).height());
				var pht = parseInt($(obj).parent().height());
				var wt = parseInt(obj.style.width);
				var pwt = parseInt($(obj).parent().width());
				var top = obj.offsetParent.offsetTop;
				var left = obj.offsetParent.offsetLeft;
				var pdl = parseInt($(obj).css('paddingLeft'));
				var pdt = parseInt($(obj).css('paddingTop'));
				return {
					ht: ht,
					wt: wt,
					top: top,
					left: left,
					pdl: pdl,
					pdt: pdt,
					pht: pht,
					pwt: pwt
				};
			},

			addRowProp: function () {
                var self = this;
				$('tbody tr', g.bDiv).each(function () {
                    self.doRowProp($(this)) ;
				});
			},

            doRowProp:function(tr){
                $(tr).click(function (e) {
                    var obj = (e.target || e.srcElement);
                    if (obj.href || obj.type) return true;
                    $(tr).toggleClass('trSelected');
                    if (p.singleSelect) $(tr).siblings().removeClass('trSelected');
                    /** Mek **/
                    //if row is selected check it
                    if(p.checkboxSelection){
                        if($(tr).hasClass('trSelected')){
                            $(tr).find(':checkbox').attr('checked',true);
                        }
                        else{
                            $(tr).find(':checkbox').attr('checked',false);
                        }
                        g.validateT_bodyCheckbox()
                    }

                }).mousedown(function (e) {
                        if (e.shiftKey) {
                            $(tr).toggleClass('trSelected');
                            g.multisel = true;
                            this.focus();
                            $(g.gDiv).noSelect();
                        }
                    }).mouseup(function () {
                        if (g.multisel) {
                            g.multisel = false;
                            $(g.gDiv).noSelect(false);
                        }
                    }).hover(function (e) {
                        if (g.multisel) {
                            $(tr).toggleClass('trSelected');
                        }
                    }, function () {});
                if ($.browser.msie && $.browser.version < 7.0) {
                    $(tr).hover(function () {
                        $(tr).addClass('trOver');
                    }, function () {
                        $(tr).removeClass('trOver');
                    });
                }
            },

			pager: 0,
			
            /** Mek **/
            //Checkbox Selection event
            
            toggleT_headCheckbox: function(_param){ //alert(_param)
                var checked = true;
				if(typeof _param == "undefined"){
					checked = false;
				}
                $('thead tr th #checkAllid:checkbox', g.hDiv).attr('checked',checked);    
            },
            
            validateT_bodyCheckbox: function(){
                var all_checked = true;
                $("tbody tr td :checkbox[name='checkAll']", g.bDiv).each(function() { 
                    var chk = $(this).is(":checked");
                    if(!chk){
                         all_checked = false;
                    }
                });
	
                if(all_checked){
                    g.toggleT_headCheckbox('checked');
                }
                else{
                    g.toggleT_headCheckbox();
                }
            },
            
            addT_headCheckboxEvent: function () {
                //Table Header                
                $('thead tr th #checkAllid:checkbox', g.hDiv).change(function() {
                    var group = 'tbody tr td :checkbox[name=' + $(this).attr('name') + ']';
                    $(group, g.bDiv).attr('checked', $(this).is(':checked'));
                    $('tbody tr', g.bDiv).each(function () {
                        var chk = $(this).find(':checkbox').is(':checked');
                        if(chk){
                             $(this).addClass('trSelected');
                        }
                        else{
                             $(this).removeClass('trSelected');
                        }
    				});
                });       
			},
            
            addT_bodydCheckboxEvent: function () {
                var self = this;
                 $("tbody tr td :checkbox[name='checkAll']", g.bDiv).live('change',function() {
                    var chk = $(this).is(':checked');
                    if(chk){
                         $(this).parent().parent().parent().addClass('trSelected');
                    }
                    else{
                          $(this).parent().parent().parent().removeClass('trSelected');
                    }
                    g.validateT_bodyCheckbox()
                });                
			},

            //Subgrid Menu Event
            addSubgridMenuEvent: function(){
                var self = this;
                $("tbody tr.master-row td div a", g.bDiv).live('click',function() {
                    var expander = $(this);
                    var id = $(this).attr('value');
                    var myclass = $(this).attr('class');
                    var parent_tr = $(this).parent().parent().parent();
                    if(myclass == 'expand'){ //fetch data and change class to collapse
                        if(typeof p.before_expand == 'function'){
                            p.before_expand(parent_tr);
                        }
                        else{
                            //console.log('Func not executed');
                        }
                        parent_tr.addClass('trSelected');
                        //if url is false then it's either json data or xml
                        var sub_grid_data = false;
                        if(p.subGrid.url){//Fetch data via ajax
                            var param = 'id='+id;
                            $('.pReload', this.pDiv).addClass('loading');
                            //Get Data
                            $.ajax({
                                type: p.subGrid.method,
                                url: p.subGrid.url,
                                data: param,
                                dataType: p.subGrid.dataType,
                                success: function (data) {
                                    $('.pReload', this.pDiv).removeClass('loading');
                                    if(data.code == 0){
                                        sub_grid_data = data.rows;
                                        expander.attr('class','collapse');
                                        expander.attr('title','Click to collapse');
                                    }
                                    else{
                                        sub_grid_data = data.rows;
                                        expander.attr('class','collapse');
                                        expander.attr('title','Click to collapse');
                                    }
                                    renderSubgrid(sub_grid_data);
                                },
                                error: function (XMLHttpRequest, textStatus, errorThrown) {
                                    try {
                                        if (p.onError) p.onError(XMLHttpRequest, textStatus, errorThrown);
                                    } catch (e) {}
                                }
                            });
                        }
                        else{  //alert('others')
                        }

                        function renderSubgrid(param_sub){
                            //console.log(parent_tr);
                            if(p.subGrid.colModel){
                                var thead = document.createElement('thead');
                                var tr = document.createElement('tr');
                                for (var i = 0; i < p.subGrid.colModel.length; i++) {
                                    var cm = p.subGrid.colModel[i];
                                    var th = document.createElement('th');
                                    if(cm.editable){//Cell is editable
                                        $(th).attr('editable', 'yes');
                                        $(th).attr('form', cm.editable.form);
                                        $(th).attr('validate', cm.editable.validate);
                                        $(th).attr('default', cm.editable.defval);
                                        if(cm.editable.bclass){
                                            $(th).attr('bclass', cm.editable.bclass);
                                        }
                                    }
                                    else{
                                        $(th).attr('editable', 'no');
                                    }
                                    //th.innerHTML = cm.display;
                                    $(th).attr('field', cm.name);
                                    if (cm.align) {
                                        th.align = cm.align;
                                    }
                                    if (cm.width) {
                                        $(th).attr('width', cm.width+'px');
                                    }
                                    $(th).append($("<div />").attr('style','text-align:'+cm.align+'; width:'+cm.width+'px; padding: 0px 5px;').html(cm.display));
                                    $(tr).append(th);
                                }
                                $(thead).append(tr);

                                var next_tr = $('<tr />').attr('class','parent_tr'+id+' sub-row');
                                var td = $('<td />').attr('style','padding-left: 30px;').attr('colspan',p.colModel.length +1);
                                var inner_table = $('<table />').attr('class','subtable subtable-bordered').attr('parent-id',id);
                                inner_table.append(thead);
                                // Add the data to the tbody
                                if(param_sub){
                                    //console.log(sub_grid_data);
                                    var tbody =  $('<tbody />').addClass('sub');
                                    for(var z in param_sub){
                                        var tr = $('<tr />');
                                        var row_items = param_sub[z];
                                        tr.attr('data-id',row_items['id']);
                                        tr.addClass('inner-sub-row');
                                        tr.attr('parent_id',id);
                                        var extra_data_str = "";

                                        //Storing extra info that may need to be passed back to the server
                                        if(row_items.extra_data){
                                            for(var c in row_items.extra_data){
                                                extra_data_str += ""+c+"=>"+row_items.extra_data[c]+","
                                            }
                                            $(tr).attr('extra-data',extra_data_str);
                                        }
                                        //Check if this data row contain some properties, like property.bg_color a css background color class for this row , property.edit_row a yes or no value for preventing this row from being editable
                                        if(row_items.property){
                                            if(row_items.property.bg_color){//Set the rows background color if any
                                                $(tr).addClass(row_items.property.bg_color);
                                            }
                                            if(row_items.property.edit_row){//Set the rows editable state that will be applied when beginEdit is invoked.
                                                $(tr).attr('edit_row',row_items.property.edit_row);
                                            }
                                        }

                                        for (var k = 0; k < p.subGrid.colModel.length; k++){
                                            var pos = p.subGrid.colModel[k]['align'];
                                            var p_width = p.subGrid.colModel[k]['width'];
                                            var td_ = $('<td />').attr('align',pos).html($("<div />").attr('style','text-align:'+pos+'; padding: 0px 5px; width:'+p_width+'px;').html(row_items['cell'][k]));
                                            $(td_).attr('data-id', row_items['cell'][k]);
                                            $(td_).attr('field', p.subGrid.colModel[k]['name']);
                                            if(p.subGrid.colModel[k]['editable']){
                                                $(td_).attr('editable', 'yes');
                                                $(td_).attr('form', p.subGrid.colModel[k]['editable']['form']);
                                                $(td_).attr('validate', p.subGrid.colModel[k]['editable']['validate']);
                                                $(td_).attr('default', p.subGrid.colModel[k]['editable']['defval']);
                                                if(p.subGrid.colModel[k]['editable']['bclass']){
                                                    $(td_).attr('bclass', p.subGrid.colModel[k]['editable']['bclass']);
                                                }
                                            }
                                            else{
                                                $(td_).attr('editable', 'no');
                                            }

                                            tr.append(td_);
                                        }
                                        self.bindSubGridRowEvent(tr);
                                        tbody.append(tr);
                                    }
                                    inner_table.append(tbody);
                                }
                                else{
                                    var tbody =  $('<tbody />').addClass('sub');
                                    inner_table.append(tbody);
                                }

                                //If there are action buttons include them
                                var action_div = '';
                                if(p.subGrid.formFields){
                                    action_div = $('<div />').addClass('sub-grid-actions-container');
                                    for(var f in p.subGrid.formFields){
                                        var action_b = p.subGrid.formFields[f];
                                        if(!action_b.separator){
                                            var b_type = action_b['type'];
                                            var b_name = action_b['name'];
                                            var action_a = $('<a />').addClass('sub-grid-action-a flexigrid-btn').attr('value',b_name).attr('href','javascript:void(0);').html(b_name);
                                            action_div.append(action_a);
                                        }
                                    }
                                }
                                td.append(action_div);
                                //Bind click event for tthe sub grid menus
                                $(action_div).find("a.sub-grid-action-a").click(function(){
                                    var a = $(this)
                                    var name = a.attr('value');
                                    for(var f in p.subGrid.formFields){
                                        var action_b = p.subGrid.formFields[f];
                                        if(!action_b.separator){
                                            var b_name = action_b['name'];
                                            var b_onpress = action_b['onpress'];
                                            if(b_name == name){
                                                b_onpress(name, inner_table);
                                                break;
                                            }
                                        }
                                    }
                                });
                                td.append(inner_table);
                                next_tr.html(td);
                                var ntr = parent_tr.next();
                                if(ntr.hasClass('parent_tr'+id)){
                                    ntr.remove();
                                }
                                parent_tr.after(next_tr);
                            }
                        }

                        if(typeof p.after_expand == 'function'){
                            p.after_expand(parent_tr);
                        }
                    }
                    else{
                        if(typeof p.before_collapse == 'function'){
                            p.before_collapse(parent_tr);
                        }
                        g.beginCancel();
                        parent_tr.removeClass('trSelected');
                        //console.log(id);
                        var child_row = parent_tr.next($("tr.parent_tr"+id));
                        if(child_row.hasClass('sub-row')){
                            child_row.remove();
                            $(expander).attr('class','expand');
                            expander.attr('title','Click to expand');
                        }
                        //parent_tr.next($("tr.parent_tr"+id)).remove();
                        if(typeof p.after_collapse == 'function'){
                            p.after_collapse(parent_tr);
                        }
                    }
                });
            },


            bindSubGridRowEvent:function(tr){
                var self = this;
               var DELAY = 200, clicks = 0, timer = null
                $(tr).click(function (e) {
                    $(tr).toggleClass('trSubSelected');
                    clicks++;
                    if(clicks === 1){ //Single Click
                        timer = setTimeout(function(){
                            clicks = 0; //after action perform reset click counter
                            if(p.subGrid.editable.use){
                                self.beginEdit(tr,'single-click', p.subGrid.colModel);
                            }
                        },DELAY);
                    }
                    else{ //Double Click
                        clearTimeout(timer); //prevent single click
                        clicks = 0; //after action perform reset click counter
                        if(p.subGrid.editable.use){
                            if(p.internal_doubleclick){
                                self.beginEdit(tr,'double-click',p.subGrid.colModel);
                                p.internal_doubleclick = false;
                            }
                            else{
                                if(p.subGrid.editable.edit){
                                    self.beginEdit(tr,'double-click',p.subGrid.colModel);
                                }
                            }
                        }
                    }
                }).dblclick(function(e) {
                        e.preventDefault(); //cancel system double-click event
                });
                /*$("table tbody tr.sub-row td table tbody tr", g.bDiv).live('click',function(e) {
                    var edit_tr = $(this);
                    $(edit_tr).toggleClass('trSubSelected');
                    clicks++;
                    if(clicks === 1){ //Single Click
                        timer = setTimeout(function(){
                            clicks = 0; //after action perform reset click counter
                            self.beginEdit(edit_tr,'single-click', p.subGrid.colModel);
                        },DELAY);
                    }
                    else{ //Double Click
                        clearTimeout(timer); //prevent single click
                        clicks = 0; //after action perform reset click counter
                        self.beginEdit(edit_tr,'double-click',p.subGrid.colModel);
                    }
                })
                    .live('dblclick',function(e) {
                        e.preventDefault(); //cancel system double-click event
                    });*/
            },

            beginSubAdd: function(inner_table){
                var self = this;
                var saved = self.saveEdit();
                if(saved){
                    p.in_edit_mode = false;
                    p.current_edit_tr = false;
                }
                else{
                    return;
                }

                var tr = $('<tr />');
                $(tr).attr('data-id',0);
                $(tr).attr('class','inner-sub-row new-row');
                $(tr).attr('parent_id',$(inner_table).attr('parent-id'));

                $('table.subtable thead tr:first th', g.bDiv).each( //add cell
                    function () {
                        var td = document.createElement('td');
                        var field =  $(this).attr('field');
                        $(td).attr('field',field);
                        var editable = $(this).attr('editable');
                        var default_val = $(this).attr('default');
                        var div_style = $(this).find('div').attr('style');
                        var style_arr = div_style.split(';');
                        var width_arr = style_arr[1].split(':');
                        var width = width_arr[1];
                        var style = $(this).attr('style');
                        $(td).attr('editable', editable);
                        if(editable == 'yes'){
                            $(td).attr('form', $(this).attr('form'));
                            $(td).attr('validate', $(this).attr('validate'));
                            $(td).attr('default', $(this).attr('default'));
                            if($(this).attr('default')){
                                $(td).attr('bclass', $(this).attr('bclass'));
                            }
                        }
                        td.align = this.align;
                        $(td).attr('style',style)
                        $(td).append($("<div />").attr('style','text-align:'+this.align+'; width:'+width+'').html(default_val));
                        $(td).attr('data-id', default_val);
                       // $(td).attr('abbr', $(this).attr('abbr'));
                        $(tr).append(td);
                        td = null;
                    }
                );
                self.bindSubGridRowEvent(tr);
                $(inner_table).find("tbody").prepend(tr);
                p.internal_doubleclick = true;
                $(tr).click().click().click();
            },


            /** MASTER GRID ACTIONS ********************************************/
            beginAdd: function(){
                var self = this;
                var saved = self.saveEdit();
                if(saved){
                    p.in_edit_mode = false;
                    p.current_edit_tr = false;
                }
                else{
                    return;
                }

                var tr = document.createElement('tr');
                $(tr).attr('data-id',0);
                $(tr).attr('class','hover-me master-row new-row');

                $('thead tr:first th', g.hDiv).each( //add cell
                    function () {
                        var td = document.createElement('td');
                        var field =  $(this).attr('field');
                        $(td).attr('field',field);
                        var idx = $(this).attr('axis').substr(3);
                        var editable = $(this).attr('editable');
                        var default_val = $(this).attr('default');
                        var div_style = $(this).find('div').attr('style');
                        var style_arr = div_style.split(';');
                        var width_arr = style_arr[1].split(':');
                        var width = width_arr[1];
                        var style = $(this).attr('style');
                        $(td).attr('editable', editable);
                        if(editable == 'yes'){
                            $(td).attr('form', $(this).attr('form'));
                            $(td).attr('validate', $(this).attr('validate'));
                            $(td).attr('default', $(this).attr('default'));
                            if($(this).attr('bclass')){
                                $(td).attr('bclass', $(this).attr('bclass'));
                            }
                        }
                        td.align = this.align;
                        $(td).attr('style',style)

                        if(field == 'subgrid_header'){
                            $(td).append($("<div />").attr('style','text-align:left; width:20px').html("<a  href='javascript:void(0);' class='expand' value='0' style='padding: 1px 10px'>&nbsp;</a>"));
                        }
                        else if(field == 'chk_field'){
                            $(td).append($("<div />").attr('style','text-align:left; width:30px').html("<input type='checkbox' name='checkAll' value='1'/>"));
                        }
                        else if(field == 'id'){
                            $(td).append($("<div />").attr('style','text-align:'+this.align+'; width:'+width+'').html(0));
                        }
                        else{
                            $(td).append($("<div />").attr('style','text-align:'+this.align+'; width:'+width+'').html(default_val));
                        }
                        $(td).attr('data-id', default_val);
                        $(td).attr('abbr', $(this).attr('abbr'));
                        $(tr).append(td);
                        td = null;
                    }
                );
                self.doRowProp(tr);
                if($("table tbody tr:first-child", g.bDiv).hasClass('erow')){

                }
                else{
                    $(tr).addClass('erow');
                }
                $("table tbody.master", g.bDiv).prepend(tr);
                p.internal_doubleclick = true;
                $(tr).click().click().click();
            },

            bindEditng: function(){
                var self = this;
                var DELAY = 200, clicks = 0, timer = null
                $("table tbody tr.master-row", g.bDiv).live('click',function(e) {
                    var edit_tr = $(this);
                    clicks++;
                    if(clicks === 1){ //Single Click
                        timer = setTimeout(function(){
                            clicks = 0; //after action perform reset click counter
                            self.beginEdit(edit_tr,'single-click', p.colModel);
                        },DELAY);
                    }
                    else{ //Double Click
                        clearTimeout(timer); //prevent single click
                        clicks = 0; //after action perform reset click counter
                        if(p.internal_doubleclick){
                            self.beginEdit(edit_tr,'double-click',p.colModel);
                            p.internal_doubleclick = false;
                        }
                        else{
                            if(p.editable.edit){
                                self.beginEdit(edit_tr,'double-click',p.colModel);
                            }
                        }
                    }
                })
                .live('dblclick',function(e) {
                        e.preventDefault(); //cancel system double-click event
                });
            },

            beginEdit: function(param_tr,click_type,colModel){
                var self = this;

                if(!param_tr){
                    return;
                }
                var beforeRender_callback = p.subGrid.editable.beforeRender;
                var afterRender_callback = p.subGrid.editable.afterRender;
                if(param_tr.hasClass('master-row')){
                    var beforeRender_callback = p.editable.beforeRender;
                    var afterRender_callback = p.editable.afterRender;
                }

                //Fire Callback for Before Rendering
                if(typeof beforeRender_callback == 'function'){
                    beforeRender_callback(param_tr);
                }


                var self = this;
                if(p.current_edit_tr){
                    if($(param_tr).attr('data-id') == p.current_edit_tr.attr('data-id')){
                        //Still editing the same row no need to reset the element
                        return;
                    }
                }
                var saved = self.saveEdit();
                if(saved){
                    p.in_edit_mode = false;
                    p.current_edit_tr = false;
                }
                else{
                    return;
                }

                if(click_type == 'single-click'){
                    if(p.in_edit_mode){

                    }
                    else{
                        return;
                    }
                }
                else if(click_type == 'double-click'){
                    //Check the tr attribute for edit_row, if it has it and the status is no then don't make the row editable if does not have it then allow for row editing
                    var edit_row = $(param_tr).attr('edit_row');
                    if(edit_row){
                        if(edit_row == 'no' || edit_row == 'No'){
                            self.gridMessage('Row Edit', 'Editing is disabled for this record.', 'error');
                            /*if(typeof p.callback == 'function'){
                                p.callback('error','Row Edit','Editing is disabled for this record.');
                            }*/
                            return;
                        }
                    }

                    /** We begin editing on double click action not single, we only edit for single click when double click action has already taken place **/
                    if(p.in_edit_mode){}
                    else{
                        p.in_edit_mode = true;
                    }
                }
                var row_id = $(param_tr).attr('data-id');
                //Injecting form elements
                param_tr.find('td').each(function(){
                    var td = $(this);
                    var field = td.attr('field');
                    if(td.attr('editable') == 'yes'){
                        var pr_controls = {}
                        for (var i = 0; i < colModel.length; i++) {
                            var name = colModel[i]['name'];
                            if(name == field){
                                pr_controls = colModel[i];
                                break;
                            }
                        }
                        //console.log(pr_controls);
                        var pr = {
                            el_id : field+'_'+row_id,
                            el_name: field,
                            el_type: pr_controls['editable']['form'],
                            el_default:pr_controls['editable']['defval'],
                            el_value:td.attr('data-id'),
                            el_readonly:(pr_controls['editable']['readonly']) ? 'readonly' : '',
                            el_class:(pr_controls['editable']['bclass']) ? pr_controls['editable']['bclass'] : '',
                            el_maxlength:(pr_controls['editable']['maxlength']) ? pr_controls['editable']['maxlength'] : '',
                            el_placeholder:(pr_controls['editable']['placeholder']) ? pr_controls['editable']['placeholder'] : ''
                        }
                        if(pr_controls['editable']['form'] == 'select'){
                            pr['el_options'] =  pr_controls['editable']['options'];
                        }
                        var el = self.getFormElement(pr); //Get the form element for the editing
                        td.find('div').html(el);
                    }
                    else{
                        //console.log(field+' not editable')
                    }
                });

                //Fire a change on all select element
                param_tr.find('td div select').each(function(){
                    var select = $(this);
                    var current_val = select.val();
                    var dfault = select.parents('div td').attr('data-id');
                    var set_val = '';
                    select.change();
                    select.find('option').each(function(){
                        var opt = $(this);
                        if(opt.html() == dfault){
                            set_val = opt.val();
                        }
                    });
                    select.val(set_val);
                });

                //Store the modified row
                p.current_edit_tr = param_tr;

                //Fire Callback for after Rendering
                if(typeof afterRender_callback == 'function'){
                    afterRender_callback(param_tr);
                }
            },


            getFormElement: function(params){
                var self = this;
                var el = null;

                if(params.el_type == 'hidden'){
                    var hid =$("<input />")
                        .attr('type','hidden')
                        .attr('name',params.el_name)
                        .attr('value',(params.el_value)? params.el_value: params.el_default)
                        .attr('class',params.el_class)
                        .attr('id',params.el_id);

                    var lab = $("<span />").html(params.el_value);
                    el = $("<span />").append(lab).append(hid);
                }
                else{
                    if(params.el_type == 'text'){
                        el = $("<input />")
                            .attr('type','text')
                            .attr('name',params.el_name)
                            .attr('value',(params.el_value)? params.el_value: params.el_default)
                            .attr('style','width:inherit;')
                            .attr('class',params.el_class)
                            .attr('id',params.el_id);
                    }
                    if(params.el_type == 'select'){
                        el = $("<select />")
                            .attr('name',params.el_name)
                            .attr('style','width:inherit;')
                            .attr('class',params.el_class)
                            .attr('id',params.el_id);
                        var setvalue = params.el_value;
                        if(!setvalue){
                            setvalue = params.el_default;
                        }
                        for(var t in params.el_options){
                            var val = params.el_options[t]['id'];
                            var title = params.el_options[t]['name'];
                            //before we compare these strings we have to remove any specail characters theat both string may contain.

                            //console.log(title+" == "+setvalue);
                            var title_c = title.replace(/[^\w\s]/gi,'');
                            var setvalue_c = setvalue.replace(/[^\w\s]/gi,'');
                            if(title_c.trim() == setvalue_c.trim()){
                                //console.log(title_c.trim()+" == "+setvalue_c.trim());
                                el.append($("<option />")
                                    .attr('value',val)
                                    .attr('selected','selected')
                                    .html(title));
                            }
                            else{
                                el.append($("<option />")
                                    .attr('value',val)
                                    .html(title));
                            }
                        }
                    }

                    if(params.el_readonly == 'readonly'){
                        el.attr('readonly','readonly');
                    }
                    if(params.el_maxlength != ''){
                        el.attr('maxlength',params.el_maxlength);
                    }
                    if(params.el_placeholder != ''){
                        el.attr('placeholder',params.el_placeholder);
                    }
                }

                return el;
            },


            beginCancel: function(){
                var self = this;
                if(p.current_edit_tr){
                    if(p.current_edit_tr.hasClass('new-row')){
                        p.current_edit_tr.remove();
                    }
                    else{
                        p.current_edit_tr.find('td').each(function(){
                            var td = $(this);
                            if(td.attr('editable') == 'yes'){
                                var field = td.attr('field');
                                var field_type = td.attr('form');
                                var field_value = td.attr('data-id');
                                if(!field_value){
                                    field_value = '';
                                }
                                td.find('div').html(field_value);
                            }
                        });
                    }
                }
                p.in_edit_mode = false;
                p.current_edit_tr = false;
            },

            beginSave: function(){
                var self = this;
                var saved = self.saveEdit();
                if(saved){
                    p.in_edit_mode = false;
                    p.current_edit_tr = false;
                }
            },

            sanitize:function(val){
                var value = val.trim();//Trim
                value = stripslashes(value);
                value = htmlspecialchars(value,'ENT_QUOTES');
                return value;

                function stripslashes (str) {
                    return (str + '').replace(/\\(.?)/g, function (s, n1) {
                        switch (n1) {
                            case '\\':
                                return '\\';
                            case '0':
                                return '\u0000';
                            case '':
                                return '';
                            default:
                                return n1;
                        }
                    });
                }

                function htmlspecialchars (string, quote_style, charset, double_encode) {
                    var optTemp = 0,
                        i = 0,
                        noquotes = false;
                    if (typeof quote_style === 'undefined' || quote_style === null) {
                        quote_style = 2;
                    }
                    string = string.toString();
                    if (double_encode !== false) { // Put this first to avoid double-encoding
                        string = string.replace(/&/g, '&amp;');
                    }
                    string = string.replace(/</g, '&lt;').replace(/>/g, '&gt;');

                    var OPTS = {
                        'ENT_NOQUOTES': 0,
                        'ENT_HTML_QUOTE_SINGLE': 1,
                        'ENT_HTML_QUOTE_DOUBLE': 2,
                        'ENT_COMPAT': 2,
                        'ENT_QUOTES': 3,
                        'ENT_IGNORE': 4
                    };
                    if (quote_style === 0) {
                        noquotes = true;
                    }
                    if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
                        quote_style = [].concat(quote_style);
                        for (i = 0; i < quote_style.length; i++) {
                            // Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
                            if (OPTS[quote_style[i]] === 0) {
                                noquotes = true;
                            }
                            else if (OPTS[quote_style[i]]) {
                                optTemp = optTemp | OPTS[quote_style[i]];
                            }
                        }
                        quote_style = optTemp;
                    }
                    if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
                        string = string.replace(/'/g, '&#039;');
                    }
                    if (!noquotes) {
                        string = string.replace(/"/g, '&quot;');
                    }

                    return string;
                }
            },

            saveEdit: function(){
                var self = this;
                var temp_tr = p.current_edit_tr;

                if(p.in_edit_mode && p.current_edit_tr){ //Start saving
                    var save = {};
                    var has_error = false;
                    //Validate all form fields first
                    var error_string = "";
                    var col_headers = p.colModel;

                    var before_save_callback = p.editable.beforeSave;
                    if(p.current_edit_tr.hasClass('inner-sub-row')){
                        col_headers = p.subGrid.colModel;
                        before_save_callback = p.subGrid.editable.beforeSave;
                    }

                    p.current_edit_tr.find('td').each(function(){
                        var td = $(this);
                        if(td.attr('editable') == 'yes'){
                            var validate_rules = td.attr('validate');
                            var field_name = td.attr('field');
                            var field_type = td.attr('form');
                            var new_val = '';
                            var obj_node = null;
                            if(field_type == 'text'){
                                obj_node = td.find('div input');
                                new_val = obj_node.val();
                            }
                            else if(field_type == 'select'){
                                obj_node = td.find('div select');
                                new_val = obj_node.val();
                                if(new_val == null){
                                    new_val = '';
                                }
                            }
                            var res = self.validateFormElement(new_val,validate_rules);
                            if(res.status){ //validation pass
                                $(obj_node).removeClass('error_field');
                            }
                            else{
                                has_error = true;
                                $(obj_node).addClass('error_field');
                                for(var r in col_headers){
                                    if(col_headers[r]['name'] == field_name){
                                        error_string += col_headers[r]['display']+" => "+res.msg+"<br />";
                                    }
                                }
                            }
                        }
                    });

                    if(has_error){ //If error don't save
                        self.gridMessage('Form Errors',error_string,'error');
                        /*if(typeof p.callback == 'function'){
                            p.callback('error','Form Errors',error_string);
                        }*/
                        return false;
                    }

                    p.current_edit_tr.find('td').each(function(){
                        var td = $(this);
                        if(td.attr('editable') == 'yes'){

                            save['id'] = p.current_edit_tr.attr('data-id');
                            if(p.current_edit_tr.hasClass('inner-sub-row')){
                                save['parent_id'] = p.current_edit_tr.attr('parent_id');
                            }
                            var new_val = '';
                            var field = td.attr('field');
                            var field_type = td.attr('form');
                            var field_value = td.attr('data-id');
                            var obj_node = null;
                            if(field_type == 'select'){
                                obj_node = td.find('div select');
                                new_val = td.find('div select option:selected').text();
                                save[field] =  obj_node.val();
                            }
                            else{
                                obj_node = td.find('div input');
                                new_val = self.sanitize(obj_node.val());
                                save[field] = new_val;
                            }
                            //Add the extra data if any
                            if(p.current_edit_tr.attr('extra-data')){
                                var extra_info = p.current_edit_tr.attr('extra-data');
                                var ext_arr = extra_info.split(',');
                                var j = {};
                                for(var d in ext_arr){
                                    var s = ext_arr[d].split('=>');
                                    j[s[0]] = s[1];
                                }
                                save['extra'] = j;
                            }

                            td.find('div').html(new_val);
                            if(p.current_edit_tr.hasClass('new-row')){
                                td.attr('data-id',new_val);
                            }
                        }
                    });

                    var url = '';
                    var edt_callback = null;
                    var run_confirm = null;
                    var confirm_msg = '';
                    if(p.current_edit_tr.hasClass('master-row')){
                        url = p.editable.url;
                        edt_callback = typeof p.editable.callback;
                        run_confirm =  p.editable.confirmSave;
                        confirm_msg =  p.editable.confirmSaveText;
                    }
                    else if(p.current_edit_tr.hasClass('inner-sub-row')){
                        url = p.subGrid.editable.url;
                        edt_callback = typeof p.subGrid.editable.callback;
                        run_confirm =  p.subGrid.editable.confirmSave;
                        confirm_msg =  p.subGrid.editable.confirmSaveText;
                    }
                    //call Before save
                    var run = true;
                    if(typeof before_save_callback == "function"){
                        run = before_save_callback();
                    }
                    if(!run){
                        return false;
                    }

                    if(run_confirm){
                        if(typeof jConfirm == "function"){
                            jConfirm(confirm_msg, 'Confirm', function(confirmation) {
                                if(confirmation){
                                    self.do_save(url,save,temp_tr);
                                }
                                else{//Resume Editing
                                    temp_tr.click().click().click();
                                }
                            });
                        }
                        else{//use normal js confirm
                            //jLib.modal_blanket_show();
                            var r=window.confirm(confirm_msg);
                            if (r){
                                //jLib.modal_blanket_hide();
                                self.do_save(url,save,temp_tr);
                            }
                            else{//Resume Editing
                                temp_tr.click().click().click();
                            }
                        }
                    }
                    else{
                        self.do_save(url,save,temp_tr);
                    }
                }
                return true;
            },


            do_save:function(url,save,temp_tr){
                var self = this;
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: save,
                    dataType: 'json',
                    success: function (response) {
                        $('.pReload', this.pDiv).removeClass('loading');
                        if(response.code == 0){
                            self.gridFooterNotify(response.msg,'success');
                            if(response.id){
                                temp_tr.attr('data-id',response.id).attr('id','row'+response.id);
                                temp_tr.find('td:first-child div a').attr('value',response.id);
                                if(temp_tr.hasClass('new-row')){
                                    temp_tr.removeClass('new-row')
                                }
                            }
                            if(response.extra_data){
                                var extra_data_str = "";
                                for(var c in response.extra_data){
                                    extra_data_str += ""+c+"=>"+response.extra_data[c]+","
                                }
                                $(temp_tr).attr('extra-data',extra_data_str);
                            }

                            if(save['id'] > 0){
                                if(p.reload_after_edit){
                                    self.populate();
                                }
                            }
                            else{
                                if(p.reload_after_add){
                                    self.populate();
                                }
                            }
                        }
                        else{
                            //self.gridFooterNotify(response.msg,'error');
                            self.gridMessage('Status',response.msg,'error');
                        }

                        //For Callbacks if any
                        /*if(temp_tr.hasClass('master-row')){
                         if(typeof p.editable.callback == 'function' ){
                         p.editable.callback(response);
                         }
                         }
                         else if(temp_tr.hasClass('inner-sub-row')){
                         if(typeof p.subGrid.editable.callback == 'function' ){
                         p.subGrid.editable.callback(response);
                         }
                         }*/
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        try {
                            if (p.onError) p.onError(XMLHttpRequest, textStatus, errorThrown);
                        } catch (e) {}
                    }
                });
            },

            validateFormElement: function(value,rules){
                var self = this;
                var $return = {status:true,msg:''};
                var validate_arr = rules.split(',');
                for(var r in validate_arr){
                    var rule = validate_arr[r];
                    if(rule == 'empty'){
                        value = value.trim();
                        if(!value){
                            $return.status = false;
                            $return.msg = '* This field is required';
                        }
                    }
                    if(rule == 'numeric'){
                        var pattern = eval(/^-?\d+\.?\d*$/);
                        if(!pattern.test(value)){
                            $return.status = false;
                            $return.msg = '* Not a valid number';
                        }
                    }
                    if(rule == 'integer'){
                        var pattern = eval(/^[\-\+]?\d+$/);
                        if(!pattern.test(value)){
                            $return.status = false;
                            $return.msg = '* Not a valid integer';
                        }
                    }
                    if(rule == 'moneyNumber'){
                        var pattern = eval(/^[0-9\,]+$/);
                        if(!pattern.test(value)){
                            $return.status = false;
                            $return.msg = '* Invalid money number';
                        }
                    }
                    if(rule == 'moneyDecimal'){
                        var pattern = eval(/^[\-\+]?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)$/);
                        if(!pattern.test(value)){
                            $return.status = false;
                            $return.msg = '* Invalid floating money decimal. Eg 0.00';
                        }
                    }
                    if(rule == 'decimalNumber'){
                        var pattern = eval(/^[\-\+]?(?:\d+|\d{1,3}(?:\d{3})+)(?:\.\d+)$/);
                        if(!pattern.test(value)){
                            $return.status = false;
                            $return.msg = '* Invalid floating decimal number. Eg 0.00';
                        }
                    }
                    if(rule == 'onlyNumber'){
                        var pattern = eval(/^[0-9\ ]+$/);
                        if(!pattern.test(value)){
                            $return.status = false;
                            $return.msg = '* Numbers only';
                        }
                    }
                    if(rule == 'onlyLetter'){
                        var pattern = eval(/^[a-zA-Z\ \']+$/);
                        if(!pattern.test(value)){
                            $return.status = false;
                            $return.msg = '* Letters only';
                        }
                    }
                    if(rule == 'email'){
                        var pattern = eval( /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/);
                        if(!pattern.test(value)){
                            $return.status = false;
                            $return.msg = '* Invalid email address';
                        }
                    }
                }
                return $return;
            },

            //Message displays for the grid
            gridFooterNotify:function(txt,type){
                var self = this;
                var cl = '';
                if(!type){
                    type = 'flex-info';
                }
                if(type == 'success'){
                    cl = 'flex-success';
                }
                else if(type == 'warning'){
                    cl = 'flex-warning';
                }
                else if(type == 'error'){
                    cl = 'flex-error';
                }
                else if(type == 'info'){
                    cl = 'flex-info';
                }

                var msg = "<div class='"+cl+"'  style='color:#ffffff; font-weight:bold; padding: 0px 5px;'>";
                msg += txt;
                msg += "</div>";

                $('.pDisplayMessage', self.pDiv).html(msg);
            },


            //Modal for the grid
            gridModal:function(type){
                var self = this;
                if(type == 'show'){
                    $(self.block).css({
                        top: self.bDiv.offsetTop,
                        background:'#000000'
                    });
                    $(self.gDiv).prepend(self.block);
                }
                else{
                    $(self.block).css({
                        background:'white'
                    });
                    $(self.block).remove();
                }
            },

            gridModalShow:function(){
                var self = this;
                self.gridModal('show');
            },

            gridModalHide:function(){
                var self = this;
                self.gridModal('hide');
            },


            gridMessage: function(title, txt, type ,callback){
                var self = this;
                var cl = '';
                //if modal exist close it and call new one
                var visible = $('#flex_modal_notify').is(':visible');
                if(visible){
                    $("#flex_modal_notify a.flex-close").click();
                }

                if(!type){
                    type = 'flex-alert-info';
                }

                if(type == 'success'){
                    cl = 'flex-alert-success';
                }
                else if(type == 'warning'){
                    cl = 'flex-alert-gradient';
                }
                else if(type == 'error'){
                    cl = 'flex-alert-error';
                }
                else if(type == 'info'){
                    cl = 'flex-alert-info';
                }

                var gh = $(self.bDiv).height();
                //var gtop = g.bDiv.offsetTop;

                var msg = "<div id='flex_modal_notify' class='flex-alert "+cl+"' style='position: relative; z-index: 2; width:500px; margin: 0 auto; top:"+(gh * -1)+"px;'>";
                msg += "<a class='flex-close' data-dismiss='flex-alert' style='opacity: 1; line-height: 16px;' href='javascript:void(0);'>✕</a>";
                msg += "<h4 class='flex-alert-heading'>"+title+"</h4>";
                msg += "<span><div id='flex-flashMessage' class='flex-message'>"+txt+"</div></span>";
                msg += "</div>";

                self.gridModalShow();
                $(self.gDiv).append(msg);

                $("#flex_modal_notify a.flex-close").click(function(){
                    self.gridModalHide();
                    $("#flex_modal_notify").remove();
                    if(typeof callback == 'function'){
                        callback();
                    }
                });
            }

		}; //End of g class
		if (p.colModel) { //create model if any
			thead = document.createElement('thead');
			var tr = document.createElement('tr');
            /***  MEK ***/

            //is subgrid required
            if(p.subGrid.use){
                var th = document.createElement('th');
                th.innerHTML = "";
                $(th).attr('field', 'subgrid_header');
                $(th).attr('editable', 'no');
                //$(th).attr('axis', 'colchkAll');
                th.align = 'left';
                $(th).attr('width', 20);
                th.hidden = false;
                $(tr).append(th);
            }

            //is checkbox selection required
            if(p.checkboxSelection){
                var th = document.createElement('th');
                th.innerHTML = "<input type='checkbox' name='checkAll' id='checkAllid' value=''/>";
                $(th).attr('field', 'chk_field');
                $(th).attr('editable', 'no');
				//if (cm.name && cm.sortable) {
					//$(th).attr('abbr', 'checkAll');
				//}
				$(th).attr('axis', 'colchkAll');
				//if (cm.align) {
					th.align = 'left';
			//	}
				//if (cm.width) {
					$(th).attr('width', 30);
				//}
				//if ($(cm).attr('hide')) {
					th.hidden = false;
				//}
				//if (cm.process) {
				//	th.process = cm.process;
				//}
				$(tr).append(th);
            }

            
			for (var i = 0; i < p.colModel.length; i++) {
				var cm = p.colModel[i];
				var th = document.createElement('th');
				th.innerHTML = cm.display;
                $(th).attr('field', cm.name);
                if(cm.editable){
                    $(th).attr('editable', 'yes');
                    $(th).attr('form', cm.editable.form);
                    $(th).attr('validate', cm.editable.validate);
                    $(th).attr('default', cm.editable.defval);
                }
                else{
                    $(th).attr('editable', 'no');
                }

				if (cm.name && cm.sortable) {
					$(th).attr('abbr', cm.name);
				}
				$(th).attr('axis', 'col' + i);
				if (cm.align) {
					th.align = cm.align;
				}
				if (cm.width) {
					$(th).attr('width', cm.width);
				}
				if ($(cm).attr('hide')) {
					th.hidden = true;
				}
				if (cm.process) {
					th.process = cm.process;
				}
				$(tr).append(th);
			}
			$(thead).append(tr);
			$(t).prepend(thead);
		} // end if p.colmodel
		//init divs
		g.gDiv = document.createElement('div'); //create global container
		g.mDiv = document.createElement('div'); //create title container
		g.hDiv = document.createElement('div'); //create header container
		g.bDiv = document.createElement('div'); //create body container
		g.vDiv = document.createElement('div'); //create grip
		g.rDiv = document.createElement('div'); //create horizontal resizer
		g.cDrag = document.createElement('div'); //create column drag
		g.block = document.createElement('div'); //creat blocker
		g.nDiv = document.createElement('div'); //create column show/hide popup
		g.nBtn = document.createElement('div'); //create column show/hide button
		g.iDiv = document.createElement('div'); //create editable layer
		g.tDiv = document.createElement('div'); //create toolbar
		g.sDiv = document.createElement('div');
		g.pDiv = document.createElement('div'); //create pager container
		if (!p.usepager) {
			g.pDiv.style.display = 'none';
		}
		g.hTable = document.createElement('table');
		g.gDiv.className = 'flexigrid';
		if (p.width != 'auto') {
			g.gDiv.style.width = p.width + 'px';
		}
		//add conditional classes
		if ($.browser.msie) {
			$(g.gDiv).addClass('ie');
		}
		if (p.novstripe) {
			$(g.gDiv).addClass('novstripe');
		}
		$(t).before(g.gDiv);
		$(g.gDiv).append(t);
		//set toolbar *** Mek Change
		if (p.formFields) {
			g.tDiv.className = 'tDiv';
			var tDiv2 = document.createElement('div');
			tDiv2.className = 'tDiv2';
			for (var i = 0; i < p.formFields.length; i++) {
				var frmfield = p.formFields[i];
				if (!frmfield.separator) {
				    if(frmfield.type == 'buttom'){
                        /*<a href="javascript:void(0)" class="button icon-star">Create</a>*/

				        var btn = frmfield;
    					var btnDiv = document.createElement('div');
    					btnDiv.className = 'fbutton';
                        //$(btnDiv).addClass('button');

    					btnDiv.innerHTML = "<div><span>" + btn.name + "</span></div>";
    					if (btn.bclass) $('span', btnDiv).addClass(btn.bclass).css({
    						paddingLeft: 20
    					});
    					btnDiv.onpress = btn.onpress;
    					btnDiv.name = btn.name;
                        if(frmfield.id){
                            //btnDiv
                        }

    					if (btn.onpress) {
    						$(btnDiv).click(function () {
    							this.onpress(this.name, g.gDiv);
    						});
    					}
    					$(tDiv2).append(btnDiv);
    					if ($.browser.msie && $.browser.version < 7.0) {
    						$(btnDiv).hover(function () {
    							$(this).addClass('fbOver');
    						}, function () {
    							$(this).removeClass('fbOver');
    						});
    					}
                    }
                    else if(frmfield.type == 'select'){
                        var selDiv = document.createElement('div');
    					selDiv.className = 'fselect';
                        var selObj = frmfield;
            			var selct = document.createElement('select');
                        if(frmfield.id){
                            selct.setAttribute('id',frmfield.id);
                        }
            			for (var nx = 0; nx < selObj.options.length; nx++) {
                            var opt = document.createElement('option');
                            opt.value = selObj.options[nx].value;
                            opt.text = selObj.options[nx].name;		   
                            try{ //Standard
                                selct.add(opt,null) ;
                            }
                            catch(error){ //IE Only
                                selct.add(opt) ;
                            }
                        }

                        if (selObj.width){
                            $(selct).attr('style',"width:"+selObj.width+"px");
                        }

                        var sclass = "";
                        if (selObj.bclass){
                            sclass = "class='"+selObj.bclass+"' ";
                        }
                        if(selObj.onchange){
                             $(selct).change(function () { 
                                selObj.onchange(selObj.name, g.gDiv, this.value);
                            });
                        }
                        $(selDiv).append("&nbsp;&nbsp;<span "+sclass+" style='padding-left:20px;'>"+selObj.name+"<span/>&nbsp;&nbsp;");
                        $(selDiv).append(selct);
                        $(selDiv).append("&nbsp;&nbsp;");
                        $(tDiv2).append(selDiv);
                    }
                    else if(frmfield.type == 'text'){
                        var textDiv = document.createElement('div');
                        textDiv.className = 'ftext';
                        var textfield = frmfield;
                        var text = document.createElement('input');
                        text.setAttribute('type','text');
                        if(frmfield.id){
                            text.setAttribute('id',frmfield.id);
                        }
                        if(frmfield.fclass){
                            text.setAttribute('class',frmfield.fclass);
                        }
                        if(frmfield.default_value){
                            text.setAttribute('value',frmfield.default_value);
                        }

                        $(textDiv).append("&nbsp;&nbsp;<span "+sclass+" style='padding-left:10px;'>"+frmfield.name+"<span/>&nbsp;&nbsp;");
                        $(textDiv).append(text);
                        $(textDiv).append("&nbsp;&nbsp;");
                        $(tDiv2).append(textDiv);
                    }


				} 
                else {
					$(tDiv2).append("<div class='btnseparator'></div>");
				}
			}
			$(g.tDiv).append(tDiv2);
			$(g.tDiv).append("<div style='clear:both'></div>");
			$(g.gDiv).prepend(g.tDiv);
		}
		g.hDiv.className = 'hDiv';
		$(t).before(g.hDiv);
		g.hTable.cellPadding = 0;
		g.hTable.cellSpacing = 0;
		$(g.hDiv).append('<div class="hDivBox"></div>');
		$('div', g.hDiv).append(g.hTable);
		var thead = $("thead:first", t).get(0);
		if (thead) $(g.hTable).append(thead);
		thead = null;
		if (!p.colmodel) var ci = 0;
		$('thead tr:first th', g.hDiv).each(function () {
			var thdiv = document.createElement('div');
			if ($(this).attr('abbr')) {
				$(this).click(function (e) {
					if (!$(this).hasClass('thOver')) return false;
					var obj = (e.target || e.srcElement);
					if (obj.href || obj.type) return true;
					g.changeSort(this);
				});
				if ($(this).attr('abbr') == p.sortname) {
					this.className = 'sorted';
					thdiv.className = 's' + p.sortorder;
				}
			}
			if (this.hidden) {
				$(this).hide();
			}
			if (!p.colmodel) {
				$(this).attr('axis', 'col' + ci++);
			}
			$(thdiv).css({
				textAlign: this.align,
				width: this.width + 'px'
			});
			thdiv.innerHTML = this.innerHTML;
			$(this).empty().append(thdiv).removeAttr('width').mousedown(function (e) {
				g.dragStart('colMove', e, this);
			}).hover(function () {
				if (!g.colresize && !$(this).hasClass('thMove') && !g.colCopy) {
					$(this).addClass('thOver');
				}
				if ($(this).attr('abbr') != p.sortname && !g.colCopy && !g.colresize && $(this).attr('abbr')) {
					$('div', this).addClass('s' + p.sortorder);
				} else if ($(this).attr('abbr') == p.sortname && !g.colCopy && !g.colresize && $(this).attr('abbr')) {
					var no = (p.sortorder == 'asc') ? 'desc' : 'asc';
					$('div', this).removeClass('s' + p.sortorder).addClass('s' + no);
				}
				if (g.colCopy) {
					var n = $('th', g.hDiv).index(this);
					if (n == g.dcoln) {
						return false;
					}
					if (n < g.dcoln) {
						$(this).append(g.cdropleft);
					} else {
						$(this).append(g.cdropright);
					}
					g.dcolt = n;
				} else if (!g.colresize) {
					var nv = $('th:visible', g.hDiv).index(this);
					var onl = parseInt($('div:eq(' + nv + ')', g.cDrag).css('left'));
					var nw = jQuery(g.nBtn).outerWidth();
					var nl = onl - nw + Math.floor(p.cgwidth / 2);
					$(g.nDiv).hide();
					$(g.nBtn).hide();
					$(g.nBtn).css({
						'left': nl,
						top: g.hDiv.offsetTop
					}).show();
					var ndw = parseInt($(g.nDiv).width());
					$(g.nDiv).css({
						top: g.bDiv.offsetTop
					});
					if ((nl + ndw) > $(g.gDiv).width()) {
						$(g.nDiv).css('left', onl - ndw + 1);
					} else {
						$(g.nDiv).css('left', nl);
					}
					if ($(this).hasClass('sorted')) {
						$(g.nBtn).addClass('srtd');
					} else {
						$(g.nBtn).removeClass('srtd');
					}
				}
			}, function () {
				$(this).removeClass('thOver');
				if ($(this).attr('abbr') != p.sortname) {
					$('div', this).removeClass('s' + p.sortorder);
				} else if ($(this).attr('abbr') == p.sortname) {
					var no = (p.sortorder == 'asc') ? 'desc' : 'asc';
					$('div', this).addClass('s' + p.sortorder).removeClass('s' + no);
				}
				if (g.colCopy) {
					$(g.cdropleft).remove();
					$(g.cdropright).remove();
					g.dcolt = null;
				}
			}); //wrap content
		});
		//set bDiv
		g.bDiv.className = 'bDiv';
		$(t).before(g.bDiv);
		$(g.bDiv).css({
			height: (p.height == 'auto') ? 'auto' : p.height + "px"
		}).scroll(function (e) {
			g.scroll()
		}).append(t);
		if (p.height == 'auto') {
			$('table', g.bDiv).addClass('autoht');
		}
		//add td & row properties
		g.addCellProp();
		g.addRowProp();
        if(p.checkboxSelection){
            g.addT_headCheckboxEvent();
        }
        
		//set cDrag
		var cdcol = $('thead tr:first th:first', g.hDiv).get(0);
		if (cdcol != null) {
			g.cDrag.className = 'cDrag';
			g.cdpad = 0;
			g.cdpad += (isNaN(parseInt($('div', cdcol).css('borderLeftWidth'))) ? 0 : parseInt($('div', cdcol).css('borderLeftWidth')));
			g.cdpad += (isNaN(parseInt($('div', cdcol).css('borderRightWidth'))) ? 0 : parseInt($('div', cdcol).css('borderRightWidth')));
			g.cdpad += (isNaN(parseInt($('div', cdcol).css('paddingLeft'))) ? 0 : parseInt($('div', cdcol).css('paddingLeft')));
			g.cdpad += (isNaN(parseInt($('div', cdcol).css('paddingRight'))) ? 0 : parseInt($('div', cdcol).css('paddingRight')));
			g.cdpad += (isNaN(parseInt($(cdcol).css('borderLeftWidth'))) ? 0 : parseInt($(cdcol).css('borderLeftWidth')));
			g.cdpad += (isNaN(parseInt($(cdcol).css('borderRightWidth'))) ? 0 : parseInt($(cdcol).css('borderRightWidth')));
			g.cdpad += (isNaN(parseInt($(cdcol).css('paddingLeft'))) ? 0 : parseInt($(cdcol).css('paddingLeft')));
			g.cdpad += (isNaN(parseInt($(cdcol).css('paddingRight'))) ? 0 : parseInt($(cdcol).css('paddingRight')));
			$(g.bDiv).before(g.cDrag);
			var cdheight = $(g.bDiv).height();
			var hdheight = $(g.hDiv).height();
			$(g.cDrag).css({
				top: -hdheight + 'px'
			});
			$('thead tr:first th', g.hDiv).each(function () {
				var cgDiv = document.createElement('div');
				$(g.cDrag).append(cgDiv);
				if (!p.cgwidth) {
					p.cgwidth = $(cgDiv).width();
				}
				$(cgDiv).css({
					height: cdheight + hdheight
				}).mousedown(function (e) {
					g.dragStart('colresize', e, this);
				});
				if ($.browser.msie && $.browser.version < 7.0) {
					g.fixHeight($(g.gDiv).height());
					$(cgDiv).hover(function () {
						g.fixHeight();
						$(this).addClass('dragging')
					}, function () {
						if (!g.colresize) $(this).removeClass('dragging')
					});
				}
			});
		}
		//add strip
		if (p.striped) {
			$('tbody tr:odd', g.bDiv).addClass('erow');
		}
		if (p.resizable && p.height != 'auto') {
			g.vDiv.className = 'vGrip';
			$(g.vDiv).mousedown(function (e) {
				g.dragStart('vresize', e)
			}).html('<span></span>');
			$(g.bDiv).after(g.vDiv);
		}
		if (p.resizable && p.width != 'auto' && !p.nohresize) {
			g.rDiv.className = 'hGrip';
			$(g.rDiv).mousedown(function (e) {
				g.dragStart('vresize', e, true);
			}).html('<span></span>').css('height', $(g.gDiv).height());
			if ($.browser.msie && $.browser.version < 7.0) {
				$(g.rDiv).hover(function () {
					$(this).addClass('hgOver');
				}, function () {
					$(this).removeClass('hgOver');
				});
			}
			$(g.gDiv).append(g.rDiv);
		}
		// add pager
		if (p.usepager) {
			g.pDiv.className = 'pDiv';
			g.pDiv.innerHTML = '<div class="pDiv2"></div>';
			$(g.bDiv).after(g.pDiv);
			var html = ' <div class="pGroup"> <div class="pFirst pButton"><span></span></div><div class="pPrev pButton"><span></span></div> </div> <div class="btnseparator"></div> <div class="pGroup"><span class="pcontrol">' + p.pagetext + ' <input type="text" size="4" value="1" /> ' + p.outof + ' <span> 1 </span></span></div> <div class="btnseparator"></div> <div class="pGroup"> <div class="pNext pButton"><span></span></div><div class="pLast pButton"><span></span></div> </div> <div class="btnseparator"></div> <div class="pGroup"> <div class="pReload pButton"><span></span></div> </div> <div class="btnseparator"></div> <div class="pGroup"><span class="pPageStat"></span></div> <div class="btnseparator"></div> <div class="pGroup"><span class="pDisplayMessage"></span></div>';
			$('div', g.pDiv).html(html);
			$('.pReload', g.pDiv).click(function () {
				g.populate()
			});
			$('.pFirst', g.pDiv).click(function () {
				g.changePage('first')
			});
			$('.pPrev', g.pDiv).click(function () {
				g.changePage('prev')
			});
			$('.pNext', g.pDiv).click(function () {
				g.changePage('next')
			});
			$('.pLast', g.pDiv).click(function () {
				g.changePage('last')
			});
			$('.pcontrol input', g.pDiv).keydown(function (e) {
				if (e.keyCode == 13) g.changePage('input')
			});
			if ($.browser.msie && $.browser.version < 7) $('.pButton', g.pDiv).hover(function () {
				$(this).addClass('pBtnOver');
			}, function () {
				$(this).removeClass('pBtnOver');
			});
			if (p.useRp) {
				var opt = '',
					sel = '';
				for (var nx = 0; nx < p.rpOptions.length; nx++) {
					if (p.rp == p.rpOptions[nx]) sel = 'selected="selected"';
					else sel = '';
					opt += "<option value='" + p.rpOptions[nx] + "' " + sel + " >" + p.rpOptions[nx] + "&nbsp;&nbsp;</option>";
				}
				$('.pDiv2', g.pDiv).prepend("<div class='pGroup'><select name='rp'>" + opt + "</select></div> <div class='btnseparator'></div>");
				$('select', g.pDiv).change(function () {
					if (p.onRpChange) {
						p.onRpChange(+this.value);
					} else {
						p.newp = 1;
						p.rp = +this.value;
						g.populate();
					}
				});
			}
			//add search button
			if (p.searchitems) {
				$('.pDiv2', g.pDiv).prepend("<div class='pGroup'> <div class='pSearch pButton'><span></span></div> </div>  <div class='btnseparator'></div>");
				$('.pSearch', g.pDiv).click(function () {
					$(g.sDiv).slideToggle('fast', function () {
						$('.sDiv:visible input:first', g.gDiv).trigger('focus');
					});
				});
				//add search box
				g.sDiv.className = 'sDiv';
				var sitems = p.searchitems;
				var sopt = '', sel = '';
				for (var s = 0; s < sitems.length; s++) {
					if (p.qtype == '' && sitems[s].isdefault == true) {
						p.qtype = sitems[s].name;
						sel = 'selected="selected"';
					} else {
						sel = '';
					}
					sopt += "<option value='" + sitems[s].name + "' " + sel + " >" + sitems[s].display + "&nbsp;&nbsp;</option>";
				}
				if (p.qtype == '') {
					p.qtype = sitems[0].name;
				}
				$(g.sDiv).append("<div class='sDiv2'>" + p.findtext + 
						"&nbsp; <input type='text' value='" + p.query +"' size='30' name='q' class='qsbox' style='padding:2px;'/> "+
						"&nbsp; On &nbsp;<select name='qtype' style='padding:2px;' class='qscol'>" + sopt + "</select> &nbsp; <button type='button' class='flex_search_btn'>Search</button>&nbsp; <button type='button' class='flex_close_search_btn'>Clear Search</button></div>");
				//Split into separate selectors because of bug in jQuery 1.3.2
                $('button.flex_search_btn', g.sDiv).click(function (e) {
					g.doSearch();
				});
                $('button.flex_close_search_btn', g.sDiv).click(function (e) {
                    g.closeSearch();
                });
				$('input[name=q]', g.sDiv).keydown(function (e) {
					if (e.keyCode == 13) {
						g.doSearch();
					}
				});
				$('select[name=qtype]', g.sDiv).keydown(function (e) {
					if (e.keyCode == 13) {
						g.doSearch();
					}
				});
				$('input[value=Clear]', g.sDiv).click(function () {
					$('input[name=q]', g.sDiv).val('');
					p.query = '';
					g.doSearch();                    
				});
				$(g.bDiv).after(g.sDiv);
			}
		}//End of use Pager
		$(g.pDiv, g.sDiv).append("<div style='clear:both'></div>");
		// add title
		if (p.title) {
			g.mDiv.className = 'mDiv';
			g.mDiv.innerHTML = '<div class="ftitle">' + p.title + '</div>';
			$(g.gDiv).prepend(g.mDiv);
			if (p.showTableToggleBtn) {
				$(g.mDiv).append('<div class="ptogtitle" title="Minimize/Maximize Table"><span></span></div>');
				$('div.ptogtitle', g.mDiv).click(function () {
					$(g.gDiv).toggleClass('hideBody');
					$(this).toggleClass('vsble');
				});
			}
		}
		//setup cdrops
		g.cdropleft = document.createElement('span');
		g.cdropleft.className = 'cdropleft';
		g.cdropright = document.createElement('span');
		g.cdropright.className = 'cdropright';
		//add block
		g.block.className = 'gBlock';
		var gh = $(g.bDiv).height();
		var gtop = g.bDiv.offsetTop;
		$(g.block).css({
			width: g.bDiv.style.width,
			height: gh,
			background: 'white',
			position: 'relative',
			marginBottom: (gh * -1),
			zIndex: 1,
			top: gtop,
			left: '0px'
		});
		$(g.block).fadeTo(0, p.blockOpacity);
        
		// add column control
        if(p.columnControl){
    		if ($('th', g.hDiv).length) {
    			g.nDiv.className = 'nDiv';
    			g.nDiv.innerHTML = "<table cellpadding='0' cellspacing='0'><tbody></tbody></table>";
    			$(g.nDiv).css({
    				marginBottom: (gh * -1),
    				display: 'none',
    				top: gtop
    			}).noSelect();
    			var cn = 0;
    			$('th div', g.hDiv).each(function () {
    				var kcol = $("th[axis='col" + cn + "']", g.hDiv)[0];
    				var chk = 'checked="checked"';
    				if (kcol.style.display == 'none') {
    					chk = '';
    				}
    				$('tbody', g.nDiv).append('<tr><td class="ndcol1"><input type="checkbox" ' + chk + ' class="togCol" value="' + cn + '" /></td><td class="ndcol2">' + this.innerHTML + '</td></tr>');
    				cn++;
    			});
    			if ($.browser.msie && $.browser.version < 7.0) $('tr', g.nDiv).hover(function () {
    				$(this).addClass('ndcolover');
    			}, function () {
    				$(this).removeClass('ndcolover');
    			});
    			$('td.ndcol2', g.nDiv).click(function () {
    				if ($('input:checked', g.nDiv).length <= p.minColToggle && $(this).prev().find('input')[0].checked) return false;
    				return g.toggleCol($(this).prev().find('input').val());
    			});
    			$('input.togCol', g.nDiv).click(function () {
    				if ($('input:checked', g.nDiv).length < p.minColToggle && this.checked == false) return false;
    				$(this).parent().next().trigger('click');
    			});
    			$(g.gDiv).prepend(g.nDiv);
    			$(g.nBtn).addClass('nBtn')
    				.html('<div></div>')
    				.attr('title', 'Hide/Show Columns')
    				.click(function () {
    					$(g.nDiv).toggle();
    					return true;
    				}
    			);
    			if (p.showToggleBtn) {
    				$(g.gDiv).prepend(g.nBtn);
    			}
    		}
        }
		// add date edit layer
		$(g.iDiv).addClass('iDiv').css({
			display: 'none'
		});
		$(g.bDiv).append(g.iDiv);
		// add flexigrid events
		$(g.bDiv).hover(function () {
			$(g.nDiv).hide();
			$(g.nBtn).hide();
		}, function () {
			if (g.multisel) {
				g.multisel = false;
			}
		});
		$(g.gDiv).hover(function () {}, function () {
			$(g.nDiv).hide();
			$(g.nBtn).hide();
		});
		//add document events
		$(document).mousemove(function (e) {
			g.dragMove(e)
		}).mouseup(function (e) {
			g.dragEnd()
		}).hover(function () {}, function () {
			g.dragEnd()
		});
		//browser adjustments
		if ($.browser.msie && $.browser.version < 7.0) {
			$('.hDiv,.bDiv,.mDiv,.pDiv,.vGrip,.tDiv, .sDiv', g.gDiv).css({
				width: '100%'
			});
			$(g.gDiv).addClass('ie6');
			if (p.width != 'auto') {
				$(g.gDiv).addClass('ie6fullwidthbug');
			}
		}
		g.rePosDrag();
		g.fixHeight();
		//make grid functions accessible
		t.p = p;
		t.grid = g;
		// load data
		if (p.url && p.autoload) {
			g.populate();
		}
		return t;
	};
	var docloaded = false;
	$(document).ready(function () {
		docloaded = true
	});
	$.fn.flexigrid = function (p) {
		return this.each(function () {
			if (!docloaded) {
				$(this).hide();
				var t = this;
				$(document).ready(function () {
					$.addFlex(t, p);
				});
			} else {
				$.addFlex(this, p);
			}
		});
	}; //end flexigrid
	$.fn.flexReload = function (p) { // function to reload grid
		return this.each(function () {
			if (this.grid && this.p.url) this.grid.populate();
		});
	}; //end flexReload
	$.fn.flexOptions = function (p) { //function to update general options
		return this.each(function () {
			if (this.grid) $.extend(this.p, p);
		});
	}; //end flexOptions
	$.fn.flexToggleCol = function (cid, visible) { // function to reload grid
		return this.each(function () {
			if (this.grid) this.grid.toggleCol(cid, visible);
		});
	}; //end flexToggleCol
	$.fn.flexAddData = function (data) { // function to add data to grid
		return this.each(function () {
			if (this.grid) this.grid.addData(data);
		});
	};
	$.fn.noSelect = function (p) { //no select plugin by me :-)
		var prevent = (p == null) ? true : p;
		if (prevent) {
			return this.each(function () {
				if ($.browser.msie || $.browser.safari) $(this).bind('selectstart', function () {
					return false;
				});
				else if ($.browser.mozilla) {
					$(this).css('MozUserSelect', 'none');
					$('body').trigger('focus');
				} else if ($.browser.opera) $(this).bind('mousedown', function () {
					return false;
				});
				else $(this).attr('unselectable', 'on');
			});
		} else {
			return this.each(function () {
				if ($.browser.msie || $.browser.safari) $(this).unbind('selectstart');
				else if ($.browser.mozilla) $(this).css('MozUserSelect', 'inherit');
				else if ($.browser.opera) $(this).unbind('mousedown');
				else $(this).removeAttr('unselectable', 'on');
			});
		}
	}; //end noSelect
    $.fn.flexBeginEdit = function (selected_tr) { // function to reload grid
        return this.each(function () {
            if(!this.p.editable.use){
                return;
            }
            if(this.p.editable.edit){
                if (this.grid) this.grid.beginEdit(selected_tr,'double-click',this.p.colModel);
            }
        });
    };
    $.fn.flexSaveChanges = function () { // function to reload grid
        return this.each(function () {
            if(!this.p.editable.use){
                return;
            }
            if (this.grid) this.grid.beginSave();
        });
    };
    $.fn.flexBeginAdd = function () { // function to reload grid
        return this.each(function () {
            if(!this.p.editable.use){
                return;
            }
            if(this.p.editable.add){
                if (this.grid) this.grid.beginAdd();
            }
        });
    };
    $.fn.flexCancel = function () { // function to reload grid
        return this.each(function () {
            if(!this.p.editable.use){
                return;
            }
            if (this.grid) this.grid.beginCancel();
        });
    };

/***  SUB GRID ACTION ***/
    $.fn.flexBeginSubAdd = function (inner_table) { // function to reload grid
        return this.each(function () {
            if(!this.p.subGrid.editable.use){
                return;
            }
            //if(this.p.subGrid.editable.add){
                if (this.grid) this.grid.beginSubAdd(inner_table);
            //}
        });
    };
    $.fn.flexBeginSubEdit = function (selected_tr) { // function to reload grid
        return this.each(function () {
            if(!this.p.subGrid.editable.use){
                return;
            }
            //if(this.p.subGrid.editable.edit){
                if (this.grid) this.grid.beginEdit(selected_tr,'double-click',this.p.subGrid.colModel);
           // }
        });
    };
    $.fn.flexSubSaveChanges = function () { // function to reload grid
        return this.each(function () {
            if(!this.p.subGrid.editable.use){
                return;
            }
            if (this.grid) this.grid.beginSave();
        });
    };
    $.fn.flexSubCancel = function () { // function to reload grid
        return this.each(function () {
            if(!this.p.subGrid.editable.use){
                return;
            }
            if (this.grid) this.grid.beginCancel();
        });
    };

    $.fn.flexUpdateEditableSubCol = function (colname,data) { // function to reload grid
        return this.each(function () {
            for(var x in this.p.subGrid.colModel){
                if(colname == this.p.subGrid.colModel[x]['display']){
                    if(typeof data == "object"){//For select
                        this.p.subGrid.colModel[x]['editable']['options']=data;
                    }
                    else{
                        this.p.subGrid.colModel[x]['editable']['defval']=data;
                    }
                    return;
                }
            }
        });
    };

    /** ACCESS TO GRID INTERNAL NOTIFICATION **/
    $.fn.flexNotify = function (title,txt,type) { // function to reload grid
        return this.each(function () {
            if (this.grid) this.grid.gridMessage(title,txt,type);
        });
    };


})(jQuery);


/*
 * Useful Objectfunctions for manupulating Flexigrid table
 *
 * Copyright (c) 2008 Gideon Amissah
 *
 */
var FlexObject = {
    init:function () {
        var self = this;
    },

    highlightRow:function (grid) {
        var self = this;
        $('.trSelected', grid).each(function () {
            $(this).removeClass('trSelected');
            var chk = $(this).find(':checkbox').is(':checked');
            if (chk) {
                $(this).find(':checkbox').attr('checked', false);
            }
            var has_class = false;
            if ($(this).hasClass('erow')) {
                $(this).removeClass('erow');
                has_class = true;
            }
            $(this).animate({backgroundColor:"#bbe4ff"}, "slow", "linear")
                .animate({backgroundColor:"#bbe4ff"}, 4000)
                .animate({backgroundColor:"#ffffff"}, "slow", "linear", function () {
                    // Animation complete.
                    if (has_class) {
                        $(this).addClass('erow');
                    }
                });
        });
        return;
    },

    updateGridData:function (grid, data) {
        var self = this;
        $('.trSelected', grid).each(function () {
            var tr = $(this);
            $(tr).find('td').each(function () {
                var val = data[$(this).attr('field')];
                if (typeof val != 'undefined') {
                    $(this).find('div').html(val);
                }
            })
        });
    },

    removeGridRows:function (grid) {
        var self = this;
        $('.trSelected', grid).each(function () {
            //$(this).removeClass('trSelected');
            var tr = $(this);
            var data_id =  tr.attr('data-id');
            var next = tr.next("tr.parent_tr"+data_id);
            tr.animate({backgroundColor:"#bbe4ff"}, 800, "linear")
                .animate({opacity:0}, 800, "linear", function () {
                    tr.remove();
                });
            next.animate({backgroundColor:"#bbe4ff"}, 800, "linear")
                .animate({opacity:0}, 800, "linear", function () {
                    next.remove();
                });
        });
    },

    countSelectedRows:function (grid) {
        var self = this;
        var selected_values = 0;
        $('.trSelected', grid).each(function () {
            selected_values = selected_values + 1;
        });
        return selected_values;
    },

    rowSelectedCheck:function (gridObject,grid, limit) {
        var self = this;
        var count = self.countSelectedRows(grid);
        if (limit) {
            if (count < limit) {
                //jAlert("At least "+limit+" Record(s) needs to be selected prior to this action",'Alert');
                var content = "At least " + limit + " Record(s) needs to be selected prior to this action";
                gridObject.flexNotify('Validation', content, 'error');
                return false;
            }
            else if (count > limit) {
                //jAlert("Only "+limit+" Record(s) at a time",'Alert');
                var content = "Only " + limit + " Record(s) at a time";
                gridObject.flexNotify('Validation', content, 'error');
                return false;
            }
        }
        else {
            if (count == 0) {
                //jAlert("At least 1 Record needs to be selected prior to this action",'Alert');
                var content = "At least 1 Record needs to be selected prior to this action";
                gridObject.flexNotify('Validation', content, 'error');
                return false;
            }
        }

        return true
    },

    getSelectedRowIds:function (grid) {
        var self = this;
        var selected_values = new Array();
        $('.trSelected', grid).each(function () {
            var id = $(this).attr('id');
            id = id.substring(id.lastIndexOf('row') + 3);
            selected_values.push(id);
        });
        return selected_values;
    },

    getSelectedRowColData:function (grid, col) {
        var self = this;
        var selected_values = new Array();
        $('.trSelected', grid).each(function () {
            $(this).find('td').each(function () {
                var field_value = $(this).attr('field');
                if (typeof field_value !== 'undefined' && field_value !== false && field_value == col) {
                    var content = $(this).find('div').html();
                    selected_values.push(content);
                }
            });
        });
        return selected_values;
    },

    getSelectedRows:function (grid) {
        var self = this;
        var selected_rows = new Array();
        $('.trSelected', grid).each(function () {
            selected_rows.push($(this));
        });
        return selected_rows;
    },

    // For Sub Grid ***********************************///////

    highlightSubRow:function (grid) {
        var self = this;
        $('tr.trSubSelected', grid).each(function () {
            $(this).removeClass('trSubSelected');
            $(this).animate({backgroundColor:"#bbe4ff"}, "slow", "linear")
                .animate({backgroundColor:"#bbe4ff"}, 4000)
                .animate({backgroundColor:"#ffffff"}, "slow", "linear", function () {

                });
        });
        return;
    },

    updateSubGridData:function (grid, data) {
        var self = this;
        $('tr.trSubSelected', grid).each(function () {
            var tr = $(this);
            $(tr).find('td').each(function () {
                var val = data[$(this).attr('field')];
                if (typeof val != 'undefined') {
                    $(this).find('div').html(val);
                }
            })
        });
    },

    removeSubGridRows:function (grid) {
        var self = this;
        $('tr.trSubSelected', grid).each(function () {
            //$(this).removeClass('trSelected');
            $(this).animate({backgroundColor:"#bbe4ff"}, 800, "linear")
                .animate({opacity:0}, 800, "linear", function () {
                    $(this).remove();
                });
        });
    },

    countSelectedSubRows:function (grid) {
        var self = this;
        var selected_values = 0;
        $('tr.trSubSelected', grid).each(function () {
            selected_values = selected_values + 1;
        });
        return selected_values;
    },

    rowSubSelectedCheck:function (grid, limit) {
        var self = this;
        var count = self.countSelectedSubRows(grid);
        if (limit) {
            if (count < limit) {
                //jAlert("At least "+limit+" Record(s) needs to be selected prior to this action",'Alert');
                var content = "At least " + limit + " Record(s) needs to be selected prior to this action";
                grid.flexNotify('Validation', content, 'error');
                return false;
            }
            else if (count > limit) {
                //jAlert("Only "+limit+" Record(s) at a time",'Alert');
                var content = "Only " + limit + " Record(s) at a time";
                grid.flexNotify('Validation', content, 'error');
                return false;
            }
        }
        else {
            if (count == 0) {
                //jAlert("At least 1 Record needs to be selected prior to this action",'Alert');
                var content = "At least 1 Record needs to be selected prior to this action";
                grid.flexNotify('Validation', content, 'error');
                return false;
            }
        }

        return true
    },

    getSelectedSubRowIds:function (grid) {
        var self = this;
        var selected_values = new Array();
        $('tr.trSubSelected', grid).each(function () {
            var id = $(this).attr('data-id');
            selected_values.push(id);
        });
        return selected_values;
    },

    getSelectedSubRowColData:function (grid, col) {
        var self = this;
        var selected_values = new Array();
        $('tr.trSubSelected', grid).each(function () {
            $(this).find('td').each(function () {
                var field_value = $(this).attr('field');
                if (typeof field_value !== 'undefined' && field_value !== false && field_value == col) {
                    var content = $(this).find('div').html();
                    selected_values.push(content);
                }
            });
        });
        return selected_values;
    },

    getSelectedSubRows:function (grid) {
        var self = this;
        var selected_rows = new Array();
        $('tr.trSubSelected', grid).each(function () {
            selected_rows.push($(this));
        });
        return selected_rows;
    }
};

/* when the page is loaded */
$(document).ready(function () {
    //Hiding the loader bar
    FlexObject.init();
});