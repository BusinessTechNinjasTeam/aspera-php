<?php
global $wpdb;

$transfers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aspera_package_transfers");
$transferData = json_encode(array_map(function($transfer) {
    return [
        $transfer->recipient,
        $transfer->status
    ];
}, $transfers));
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Package Transfers</h1>
    <button id="new-transfer" class="page-title-action">New Transfer</button>
    <hr class="wp-header-end">

    <h2 class="screen-reader-text">Filter package transfers list</h2>

    <ul class="subsubsub">
<!--        <li class="all"><a href="edit.php?post_type=page" class="current" aria-current="page">All <span class="count">(7)</span></a> |</li>-->
<!--        <li class="publish"><a href="edit.php?post_status=publish&amp;post_type=page">Published <span class="count">(6)</span></a> |</li>-->
<!--        <li class="draft"><a href="edit.php?post_status=draft&amp;post_type=page">Draft <span class="count">(1)</span></a></li>-->
    </ul>

    <list-table
        data-columns='[{"id": "recipientEmail","label": "Recipient Email"}, {"id": "status","label": "Status"}]'
        data-rows='<?php echo $transferData; ?>'
    ></list-table>

    <dialog
        is="add-new"
        target="new-transfer"
        data-fields='[{"id": "recipientEmail","label": "Recipient Email"}]'
    >
    </dialog>

</div>

<script>
    class ListTable extends HTMLElement {
        connectedCallback() {
            const template = document.getElementById('list-table');
            const node = document.importNode(template.content, true);
            this.appendChild(node);

            const columns = JSON.parse(this.getAttribute('data-columns'))
            columns.forEach(this.appendColumnCallback())

            const rows = JSON.parse(this.getAttribute('data-rows'))
            rows.forEach(this.appendRowCallback())
        }

        appendColumnCallback() {
            return ({id, label}) => {
                const node = document.createElement('th')
                node.id = id
                node.textContent = label
                node.scope = 'col'
                node.className = 'manage-column column-columnname'
                this.querySelector('thead tr').appendChild(node.cloneNode(true));
                this.querySelector('tfoot tr').appendChild(node.cloneNode(true));
            }
        }

        appendRowCallback() {
            return (columns, index) => {
                const node = document.createElement('tr')
                node.className = index % 2 ? '' : 'alternate'
                node.valign = 'top'
                this.querySelector('tbody').appendChild(node);

                columns.forEach(this.appendRowColumnCallback(node))
            }
        }

        appendRowColumnCallback(rowColumnNode) {
            return (content, index) => {
                const node = document.createElement('td')
                node.className = 'column-columnname'
                node.textContent = content

                // index === 0 && ['View'].forEach(this.appendRowColumnAction(node))

                rowColumnNode.appendChild(node);
            }
        }

        appendRowColumnAction(rowColumnNode) {
            const rowActionsNode = document.createElement('div')
            rowActionsNode.className = 'row-actions'
            rowColumnNode.appendChild(rowActionsNode)
            return (action) => {
                const actionNode = document.createElement('span')
                actionNode.innerHTML = `<a href="#">${action}</a>`
                rowActionsNode.appendChild(actionNode)
            }
        }
    }

    class AddNewDialog extends HTMLDialogElement {

        connectedCallback() {

            const template = document.getElementById('add-new');
            this.innerHTML = template.innerHTML + this.innerHTML

            const columns = JSON.parse(this.getAttribute('data-fields'))
            columns.forEach(this.appendFieldCallback())

            const target = document.getElementById(this.getAttribute('target'))
            target.addEventListener('click', this.showModal.bind(this))
        }

        appendFieldCallback() {
            return ({id, label}) => {
                const node = document.createElement('tr')
                node.innerHTML = `<th scope="row"><label for="${id}">${label}</label></th><td><input name="${id}" type="text" id="${id}" value="" class="regular-text"></td>`
                this.querySelector('.form-table tbody').appendChild(node);
            }
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        customElements.define('list-table', ListTable)
        customElements.define('add-new', AddNewDialog, {extends: 'dialog'})
    });
</script>

<template id="list-table">
    <table class="widefat fixed" cellspacing="0">
        <thead>
        <tr>
<!--            <th id="cb" class="manage-column column-cb check-column" scope="col"></th>-->
        </tr>
        </thead>

        <tfoot>
        <tr>
<!--            <th class="manage-column column-cb check-column" scope="col"></th>-->
        </tr>
        </tfoot>

        <tbody>
<!--        <tr class="alternate" valign="top">-->
<!--            <th class="check-column" scope="row"></th>-->
<!--            <td class="column-columnname">-->
<!--                <div class="row-actions">-->
<!--                    <span><a href="#">Action</a> |</span>-->
<!--                    <span><a href="#">Action</a></span>-->
<!--                </div>-->
<!--            </td>-->
<!--            <td class="column-columnname"></td>-->
<!--        </tr>-->
        </tbody>
    </table>
</template>

<template id="add-new">

    <h2>Add New</h2>

    <form method="dialog" style="position: absolute; top: 15px; right: 15px;">
        <button style="cursor: pointer; border: 0; background: 0 0;">Close</button>
    </form>

    <form action="admin-post.php" method="post">

        <input type="hidden" name="action" value="start_transfer">
        <input type="hidden" name="redirect" value="aspera/package-transfers.php">

        <table class="form-table" role="presentation">
            <tbody>
            </tbody>
        </table>

        <div class="submit" style="display: flex; justify-content: space-between;">
            <button type="submit" class="button button-primary">Start Transfer</button>
        </div>

    </form>

</template>
