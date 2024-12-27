<!--suppress ALL, HtmlFormInputWithoutLabel -->
<h2 style="text-align: center;margin: 30px 0 0;">{$LANG.dna.childhostmanagement}</h2>
<h3 style="text-align: center;margin: 0 0 30px">{$domain}</h3>
{if $process_error}
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
        <strong>{$LANG.dna.something_went_wrong}</strong> {$process_error}
    </div>
{/if}

{if $process_success}
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
        <strong>{$LANG.dna.saved}</strong>
    </div>
{/if}

<div class="row">
    <div class="col-md-12">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>{$LANG.dna.nameserver}</th>
                <th>{$LANG.dna.ipaddress}</th>
                <th>{$LANG.dna.action}</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$nameservers item=nameserver}
                <tr id="ns-row-{$nameserver.name}">
                    <form method="post" action="" class="ns-form">
                        <input type="hidden" name="subaction" value="modifychildhost">
                        <input type="hidden" name="nameserver" value="{$nameserver.name}">
                        <td>
                            <div class="input-group">
                                <input type="text" disabled class="form-control" value="{$nameserver.name}">
                                <span class="input-group-addon">.{$domain}</span>
                            </div>
                        </td>
                        <td>
                            <input type="text" name="newipaddress" class="form-control" value="{$nameserver.ip}" required>
                        </td>
                        <td>
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="fa fa-save"></i> {$LANG.dna.save}
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteNameserver('{$nameserver.name}')">
                                <i class="fa fa-trash"></i> {$LANG.dna.delete}
                            </button>
                        </td>
                    </form>
                </tr>
            {/foreach}
            <!-- Yeni nameserver ekleme formu -->
            <tr>
                <form method="post" action="">
                    <input type="hidden" name="subaction" value="addchildhost">
                    <td>
                        <div class="input-group">
                            <input type="text" name="nameserver" class="form-control" placeholder="nameserver" required>
                            <span class="input-group-addon">.{$domain}</span>
                        </div>
                    </td>
                    <td>
                        <input type="text" name="ipaddress" class="form-control" placeholder="IP Adresi" required>
                    </td>
                    <td>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-plus"></i> {$LANG.dna.add}
                        </button>
                    </td>
                </form>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function deleteNameserver(nameserver) {
    if (confirm('{$LANG.dna.areyousure}')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';

        var subactionInput = document.createElement('input');
        subactionInput.type = 'hidden';
        subactionInput.name = 'subaction';
        subactionInput.value = 'deletechildhost';

        var nameserverInput = document.createElement('input');
        nameserverInput.type = 'hidden';
        nameserverInput.name = 'nameserver';
        nameserverInput.value = nameserver;

        form.appendChild(subactionInput);
        form.appendChild(nameserverInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

