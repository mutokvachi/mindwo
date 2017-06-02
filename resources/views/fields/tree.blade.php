<div style="width: 100%;" class="input-group {{ ($is_disabled) ? '' : 'dx-tree-field' }}" 
     @if (!$is_disabled)
        dx_is_init="0"
        dx_tree_content = '{{ $tree }}'
        dx_tree_title = '{{ $form_title }}'
        dx_tree_default_node_txt = '{{ $item_full_path }}'
        dx_tree_item_value = '{{ ($item_value) ? $item_value : 0 }}'
        dx_tree_id = '{{ $frm_uniq_id }}_{{ $item_field }}_tree_field'
     @endif
     >
    <input readonly type="text" class="form-control dx-tree-txt-visible" name = '{{ $item_field }}_tree_txt' value='{{ $item_full_path }}'>
    <input type=hidden class="dx-tree-txt-hidden" name = '{{ $item_field }}'  value = '{{ $item_value }}' />
    @if (!$is_disabled)
        
        <span class="input-group-btn">
            <button {{ ($is_disabled) ? 'disabled' : '' }} class="btn btn-white dx-tree-btn-del" type="button" style='margin-right: 2px;'><i class='fa fa-trash-o'></i></button>
        </span>
        <span class="input-group-btn">
          <button {{ ($is_disabled) ? 'disabled' : '' }} class="btn btn-white dx-tree-btn-add" type="button"><i class='fa fa-plus'></i></button>
        </span>
    @endif
</div>