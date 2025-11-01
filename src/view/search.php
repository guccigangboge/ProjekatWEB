<small>Search all results</small>
<small>with ".*"</small>
<form action="index.php" method="get">
  <input type="hidden" name="action" value="search">
  
  <div>
    <label for="search_query">Search:</label>
    <input type="text" id="search_query" name="query" required>
  </div>
  
  <div>
    <label for="search_type">Search type:</label>
    <select id="search_type" name="type">
      <option value="blog">Blog posts</option>
      <option value="user">Users</option>
    </select>
  </div>
  
  <div>
    <input type="submit" value="Search">
  </div>
</form>
