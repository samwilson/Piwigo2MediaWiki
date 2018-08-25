<h3>{'Piwigo to MediaWiki'|translate}</h3>

<h4>{'Available MediaWiki sites'|translate}</h4>
{if ! $p2m_conf }
  <p><em>{'None found'|translate}</em></p>
{else}
  <table class="piwigo2mediawiki-wikis">
    <thead>
      <tr>
        <th>{'Number'|translate}</th>
        <th>{'Site name'|translate}</th>
        <th>{'API URL'|translate}</th>
        <th>{'Username'|translate}</th>
        <th>{'Actions'|translate}</th>
      </tr>
    </thead>
    <tbody>
      {foreach from=$p2m_conf key=id item=site}
      <tr>
        <td>{$id}</td>
        <td>{$site.sitename}</td>
        <td>{$site.url}</td>
        <td>{$site.username}</td>
        <td>
          <a href="{$admin_url}&action=edit&id={$id}" class="buttonLike">{'Edit'|translate}</a>
          <form action="{$admin_url}" method="post">
            <input type="hidden" name="page" value="{$piwigo2mediawiki_page}" />
            <input type="hidden" name="id" value="{$id}" />
            <input type="hidden" name="action" value="delete" />
            <input type="submit" value="{'Delete'|translate}" />
          </form>
        </td>
      </tr>
      {/foreach}
    </tbody>
  </table>
{/if}

<form action="{$admin_url}" method="post">
  <input type="hidden" name="page" value="{$piwigo2mediawiki_page}" />
  <input type="hidden" name="action" value="add" />
  <fieldset>
    <legend>
      {if $info.id}
        {"Edit this wiki's details"|translate}
        <input type="hidden" name="id" value="{$info.id}" />
      {else}
        {'Add a new wiki'|translate}
      {/if}
    </legend>
    <table>
      <tr>
        <th><label for="url">{'MediaWiki URL (any page):'}</label></th>
        <td><input type="url" size="80" name="url" id="url" required="required" value="{$info.url}" /></td>
      </tr>
      <tr>
        <th><label for="username">{'Username:'}</label></th>
        <td><input type="text" size="80" name="username" id="username" required="required" value="{$info.username}" /></td>
      </tr>
      <tr>
        <th><label for="password">{'Password:'}</label></th>
        <td><input type="password" size="80" name="password" id="password" required="required" value="{$info.password}" /></td>
      </tr>
      <tr>
        <th><label for="wikitext">{'Wikitext:'}</label></th>
        <td><textarea name="wikitext" id="wikitext" rows="12" cols="80">{$info.wikitext}</textarea></td>
      </tr>
      <tr>
        <th></th>
        <td>
          {if $info.id}
            <input type="submit" value="{'Save'|translate}" />
            <a href="{$admin_url}" class="buttonLike">{'Cancel'|translate}</a>
          {else}
            <input type="submit" value="{'Add'|translate}" />
          {/if}
        </td>
      </tr>
    </table>
  </fieldset>
</form>

{footer_script}
  $('#mediawiki_url').on('change', function() {
    
  });
{/footer_script}

{html_style}
  .piwigo2mediawiki-wikis form { display:inline }
{/html_style}
