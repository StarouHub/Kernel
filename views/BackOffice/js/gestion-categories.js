// Demo: Dynamic add for categories (replace with AJAX for real backend use)
document.getElementById('add-category-form').onsubmit = function(e) {
  e.preventDefault();
  const name = this.name.value.trim();
  if(!name) return;
  const ul = document.getElementById('categories-list');
  const li = document.createElement('li');
  li.textContent = name + ' ';
  const del = document.createElement('button');
  del.textContent = "Delete";
  del.onclick = () => ul.removeChild(li);
  li.appendChild(del);
  ul.appendChild(li);
  this.reset();
};
